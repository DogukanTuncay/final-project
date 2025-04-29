<?php

namespace App\Interfaces\Services\Admin;

interface BadgeServiceInterface
{
    /**
     * Tüm rozetleri al
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * ID ile rozet bul
     * 
     * @param int $id
     * @return \App\Models\Badge|null
     */
    public function find($id);
    
    /**
     * Rozet oluştur
     * 
     * @param array $data
     * @return \App\Models\Badge
     */
    public function create(array $data);
    
    /**
     * Rozeti güncelle
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data);
    
    /**
     * Rozeti sil
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id);
    
    /**
     * Rozet durumunu değiştir (aktif/pasif)
     * 
     * @param int $id
     * @return bool
     */
    public function toggleStatus(int $id): bool;
}