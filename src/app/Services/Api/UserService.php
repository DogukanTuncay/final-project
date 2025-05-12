<?php

namespace App\Services\Api;

use App\Models\User;
use App\Interfaces\Repositories\Api\UserRepositoryInterface;
use App\Interfaces\Services\Api\UserServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

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
            // Önce kullanıcının var olduğundan emin olalım
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                Log::warning('UserService: Profil güncellemesi için kullanıcı bulunamadı', ['user_id' => $userId]);
                return null;
            }

            // Güvenlik için sadece güncellenmesine izin verilen alanları filtreleyelim
            $allowedFields = ['name', 'username', 'phone', 'zip_code'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));
            
            // Hiç güncellenecek alan yoksa boşuna repository çağırmayalım
            if (empty($filteredData)) {
                return $user;
            }
            
            // Güncellemeleri loglamak için onceki değerleri kaydedelim
            $logData = [];
            foreach ($filteredData as $key => $value) {
                if ($user->{$key} != $value) {
                    $logData[$key] = [
                        'old' => $user->{$key},
                        'new' => $value
                    ];
                }
            }

            // Eğer değişiklik varsa logla
            if (!empty($logData)) {
                Log::info('UserService: Profil güncellemesi', [
                    'user_id' => $userId,
                    'changes' => $logData
                ]);
            }

            // Repository üzerinden güncellemeyi yap
            return $this->userRepository->update($userId, $filteredData);
            
        } catch (\Exception $e) {
            Log::error('UserService updateProfile error: ' . $e->getMessage(), [
                'user_id' => $userId, 
                'data' => $data
            ]);
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

    public function updatePassword(int $userId, string $currentPassword, string $newPassword)
    {
        try {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                return 'Kullanıcı bulunamadı.';
            }
            if (!Hash::check($currentPassword, $user->password)) {
                return 'Mevcut şifre hatalı.';
            }
            if ($newPassword === $currentPassword) {
                return 'Yeni şifre eski şifreyle aynı olamaz.';
            }
            $user->password = Hash::make($newPassword);
            $user->save();
            return true;
        } catch (\Exception $e) {
            Log::error('UserService updatePassword error: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);
            return 'Bir hata oluştu.';
        }
    }
} 