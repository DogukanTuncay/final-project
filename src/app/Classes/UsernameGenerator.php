<?php

namespace App\Classes;
use Illuminate\Support\Str;
use App\Models\User;

class UsernameGenerator
{
    /**
     * Kullanıcı adı oluşturur.
     *
     * @param string $string Kullanıcı adı için baz alınacak metin
     * @return string
     */
    public function generateRandomUsername($string)
    {
        // 1. Adım: Kullanıcı adını daha anlamlı hale getirmek için ilk kısmı alalım
        $string = strtolower($string);  // Küçük harflerle çalışalım
        // Özel karakterleri temizliyoruz (Aşağıdaki regex yalnızca harf ve rakamları alır)
         // Özel karakterleri temizliyoruz (Aşağıdaki regex yalnızca harf ve rakamları alır)
         $string = preg_replace('/[^a-z0-9\s]/', '', $string);

         // 2. Adım: İlk rastgele parça (ilk 2 harf ya da kelime) alınacak
         $nameParts = explode(' ', $string);  // String'i kelimelere ayıralım
         $firstPart = substr(str_shuffle($nameParts[0]), 0, rand(2, 4)); // İsimden rastgele 2-4 harf alıyoruz

         // 3. Adım: Soyadı kısmı (ya da ikinci kelime) için de benzer bir işlem uygulayalım
         $secondPart = isset($nameParts[1]) ? substr(str_shuffle($nameParts[1]), 0, rand(2, 4)) : substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, rand(2, 4)); // Eğer soyadı varsa, yoksa rastgele harfler ekle

         // 4. Adım: Rastgele sayılar ekleyerek kullanıcı adı benzersiz hale getirelim
         $randomNumber = rand(1000, 9999);
        // 4. Adım: Bazı özel karakterler ekleyerek kullanıcının adı daha şık hale gelebilir
        $specialChar = array('-', '_', '@', '#');
        $randSpecialChar = $specialChar[array_rand($specialChar)];

        // 5. Adım: Kullanıcı adı formatını oluşturuyoruz
        $username = $firstPart . $secondPart . $randSpecialChar . $randomNumber;

        // 6. Adım: Kullanıcı adı oluşturulduktan sonra, veritabanında benzersiz olup olmadığını kontrol edelim
        return $this->isUsernameUnique($username) ? $username : $this->generateRandomUsername($string);
    }

    /**
     * Kullanıcı adının veritabanında benzersiz olup olmadığını kontrol eder.
     * Eğer benzersiz değilse, yeni bir kullanıcı adı oluşturur.
     *
     * @param string $username
     * @return string
     */
    public function isUsernameUnique($username)
    {
        // Kullanıcı adının veritabanında var olup olmadığını kontrol et
        if (User::where('username', $username)->exists()) {
            // Eğer varsa, yeni bir kullanıcı adı oluştur
            return $this->generateRandomUsername($username);
        }

        return $username;
    }
}
