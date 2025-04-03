<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorse Palet Hesaplama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            flex-direction: column; 
            min-height: 100vh; 
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            flex: 1; 
            padding-top: 40px;
            padding-bottom: 20px; 
        }
        .card { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        footer {
            background-color: #e9ecef; 
            padding-top: 15px;
            padding-bottom: 15px;
            margin-top: auto; 
            width: 100%; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center text-primary">Dorse Palet Yerleşim Hesaplayıcı</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Palet Bilgisi</h5>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="palet_sayisi" class="form-label">Yüklenecek Toplam Palet Sayısı:</label>
                        <input type="number" class="form-control form-control-lg" id="palet_sayisi" name="palet_sayisi" required min="1" placeholder="Örn: 60" value="<?php echo isset($_POST['palet_sayisi']) ? htmlspecialchars($_POST['palet_sayisi']) : ''; ?>">
                        <div class="form-text">Dorsenin tek katlı 32, çift katlı 64 palet aldığını varsayıyoruz.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">Hesapla</button>
                </form>
            </div>
        </div>

        <?php
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['palet_sayisi'])) {
            $input_palet_sayisi = filter_input(INPUT_POST, 'palet_sayisi', FILTER_VALIDATE_INT);
            $mesaj = "";
            $alert_tipi = "info";

            if ($input_palet_sayisi === false || $input_palet_sayisi <= 0) {
                $mesaj = "Lütfen geçerli bir pozitif palet sayısı girin!";
                $alert_tipi = "danger";
            } else {
                $toplam_palet = $input_palet_sayisi;
                $kapasite_tek_pozisyon = 32;
                $kapasite_cift_max_palet = 64;
                $cift_kat_palet = 0;
                $tek_kat_palet = 0;
                $cift_kat_pozisyon = 0;
                $tek_kat_pozisyon = 0;
                $toplam_kullanilan_pozisyon = 0;

                if ($toplam_palet > $kapasite_cift_max_palet) {
                    $mesaj = "<strong>Hata!</strong> Girilen palet sayısı ({$toplam_palet}), dorse kapasitesini ({$kapasite_cift_max_palet} palet) aşıyor. Bu yükleme yapılamaz.";
                    $alert_tipi = "danger";
                } elseif ($toplam_palet <= $kapasite_tek_pozisyon) {
                    $tek_kat_palet = $toplam_palet;
                    $tek_kat_pozisyon = $toplam_palet;
                    $toplam_kullanilan_pozisyon = $tek_kat_pozisyon;
                    $mesaj = "<strong>{$toplam_palet} paletin tamamı tek katlı olarak yüklenebilir.</strong><br>";
                    $mesaj .= "- Tek Katlı Palet: {$tek_kat_palet}<br>";
                    $mesaj .= "- Kullanılan Yer: {$toplam_kullanilan_pozisyon} / {$kapasite_tek_pozisyon}";
                    $alert_tipi = "success";
                } else {
                    $cift_kat_pozisyon = $toplam_palet - $kapasite_tek_pozisyon;
                    $cift_kat_palet = $cift_kat_pozisyon * 2;
                    $tek_kat_palet = $toplam_palet - $cift_kat_palet;
                    $tek_kat_pozisyon = $tek_kat_palet;
                    $toplam_kullanilan_pozisyon = $cift_kat_pozisyon + $tek_kat_pozisyon;
                    $mesaj = "<strong>{$toplam_palet} palet için en verimli yükleme:</strong><br>";
                    $mesaj .= "<ul class='list-unstyled mb-0'>"; 
                    $mesaj .= "<li>✔️ <strong>{$cift_kat_palet} palet üst üste (çift katlı)</strong> konulmalı (Dorsede {$cift_kat_pozisyon} yer kaplar).</li>";
                    $mesaj .= "<li>✔️ <strong>{$tek_kat_palet} palet tek katlı</strong> konulmalı (Dorsede {$tek_kat_pozisyon} yer kaplar).</li>";
                    $mesaj .= "</ul>";
                    $mesaj .= "<hr>"; 
                    $mesaj .= "Toplamda <strong>{$toplam_kullanilan_pozisyon} / {$kapasite_tek_pozisyon}</strong> paletlik yer kullanılır.";
                    $alert_tipi = "success";
                }
            }
            
            echo '<div class="alert alert-'.$alert_tipi.' mt-4" role="alert">';
            echo '<h4 class="alert-heading">Yükleme Planı</h4>';
            echo '<p>' . $mesaj . '</p>';
            echo '</div>';
        }
        ?>

        <?php
        
        if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['palet_sayisi'])) {
            echo '<div class="alert alert-info mt-4" role="alert">';
            echo '<h4 class="alert-heading">Nasıl Çalışır?</h4>';
            echo '<p>Yüklemek istediğiniz toplam palet sayısını girin ve "Hesapla" butonuna basın. Sistem, paletleri dorsenize (32 tek kat / 64 çift kat kapasiteli) en verimli şekilde nasıl yerleştireceğinizi hesaplayacaktır.</p>';
            echo '<hr>';
            echo '<p class="mb-0">Örneğin, 60 palet için sonuç: 56 palet üst üste (28 yer), 4 palet tek katlı (4 yer) olacaktır.</p>';
            echo '</div>';
        }
        ?>

    </div> <footer class="text-center text-muted">
         <p class="mb-0">xmtaha tarafından sevgi ile kodlanmıştır ❤️</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>