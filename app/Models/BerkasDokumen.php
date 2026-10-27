<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BerkasDokumen extends Model
{
    protected $table = 'berkas_dokumen';

    protected $primaryKey = 'berkas_id';

    public $incrementing = true;

    protected $fillable = [
        'pelanggan_id',
        'nama_file',
        'file_path',
        'ukuran_file',
        'jumlah_halaman',
        'status_validasi',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'berkas_id', 'berkas_id');
    }

    /**
     * Validasi format dan ukuran berkas dokumen (Gambar 4.4 Class Diagram - UC-04).
     */
    public function validasiFormat(): bool
    {
        $ext = strtolower(pathinfo((string) $this->nama_file, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'docx', 'doc', 'jpg', 'jpeg', 'png'];
        $maxSizeBytes = 20 * 1024 * 1024; // 20 MB

        return in_array($ext, $allowedExtensions, true)
            && ((int) $this->ukuran_file <= $maxSizeBytes);
    }

    /**
     * Hitung jumlah halaman dokumen PDF secara otomatis (Gambar 4.4 Class Diagram - KF-04).
     */
    public function parseJumlahHalaman(): int
    {
        if (empty($this->file_path) || ! Storage::disk('local')->exists($this->file_path)) {
            return max(1, (int) $this->jumlah_halaman);
        }

        $content = (string) Storage::disk('local')->get($this->file_path);
        if ($content === '') {
            return max(1, (int) $this->jumlah_halaman);
        }

        $pageObjectCount = preg_match_all('/\/Type\s*\/Page(?!s)\b/i', $content, $pageMatches);
        $treeCount = 0;
        if (preg_match_all('/\/Type\s*\/Pages\b[^>]*?\/Count\s+(\d+)/is', $content, $treeMatches) && ! empty($treeMatches[1])) {
            $treeCount = max(array_map('intval', $treeMatches[1]));
        } elseif (preg_match_all('/\/Count\s+(\d+)/i', $content, $countMatches) && ! empty($countMatches[1])) {
            $treeCount = max(array_map('intval', $countMatches[1]));
        }

        $detected = max(1, (int) $pageObjectCount, (int) $treeCount);
        $this->update(['jumlah_halaman' => $detected]);

        return $detected;
    }

    /**
     * Hapus berkas fisik dari storage privat pasca pengerjaan tuntas sesuai UU PDP No. 27/2022 (NFR-S).
     */
    public function purgeBerkas(): bool
    {
        if (! empty($this->file_path) && Storage::disk('local')->exists($this->file_path)) {
            Storage::disk('local')->delete($this->file_path);
        }

        return $this->update([
            'status_validasi' => 'terhapus otomatis (UU PDP)',
        ]);
    }
}
