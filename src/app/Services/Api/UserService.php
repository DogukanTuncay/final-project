<?php

namespace App\Services\Api;

use App\Models\User;
use App\Interfaces\Repositories\Api\UserRepositoryInterface;
use App\Interfaces\Services\Api\UserServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get the authenticated user's profile information.
     *
     * @param int $userId
     * @return User|null
     */
    public function getProfile(int $userId): ?User
    {
        try {
            // İlişkileri yüklemek isteyebiliriz (örn: level)
            // Modelde $with tanımlıysa otomatik yüklenir.
            return $this->userRepository->findById($userId);
        } catch (\Exception $e) {
            Log::error('UserService getProfile error: ' . $e->getMessage(), ['user_id' => $userId]);
            return null;
        }
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @param int $userId
     * @param array $data
     * @return User|null
     */
    public function updateProfile(int $userId, array $data): ?User
    {
        try {
            // Güvenlik için sadece izin verilen alanları güncelle
            // Modeldeki $fillable alanlarından bazılarını hariç tutabiliriz (örn. email, level_id, experience_points)
            // Veya Request sınıfında bu kontrolü daha sıkı yapabiliriz.
            // Şimdilik Request'ten gelen $validated verisinin güvenli olduğunu varsayıyoruz.
            // Ancak yine de hassas olabilecek alanları filtreleyelim:
            $allowedData = Arr::except($data, ['email', 'password', 'level_id', 'experience_points', 'locale']);

            if (empty($allowedData)) {
                // Güncellenecek geçerli alan yoksa null döndür veya mevcut kullanıcıyı döndür
                return $this->userRepository->findById($userId);
            }

            return $this->userRepository->update($userId, $allowedData);
        } catch (\Exception $e) {
            Log::error('UserService updateProfile error: ' . $e->getMessage(), ['user_id' => $userId, 'data' => $data]);
            return null;
        }
    }

    /**
     * Update the authenticated user's locale.
     *
     * @param int $userId
     * @param string $locale
     * @return User|null
     */
    public function updateLocale(int $userId, string $locale): ?User
    {
        try {
            // Burada ek iş mantığı olabilir (örn: locale geçerli mi kontrolü - request'te yapıldı gerçi)
            return $this->userRepository->updateLocale($userId, $locale);
        } catch (\Exception $e) {
            Log::error('UserService updateLocale error: ' . $e->getMessage(), ['user_id' => $userId, 'locale' => $locale]);
            return null;
        }
    }
} 