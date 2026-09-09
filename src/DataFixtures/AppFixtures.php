<?php

namespace App\DataFixtures;

use App\Entity\Advertisement;
use App\Entity\AdvertisementImage;
use App\Entity\User;
use App\Entity\UserProfile;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const PASSWORD = '12345678';

    /**
     * Conturile de test. Toate sunt verificate, deci pot posta anunturi.
     */
    private const USERS = [
        ['email' => 'demo@demo.com',              'name' => 'Demo User',     'bio' => 'Cont de demonstratie. Foloseste-l ca sa te plimbi prin aplicatie.', 'city' => 'Bucuresti'],
        ['email' => 'alex.mircea@example.com',    'name' => 'Alex Mircea',   'bio' => 'Vand periodic electronice si accesorii de birou. Raspund rapid la mesaje.', 'city' => 'Cluj-Napoca'],
        ['email' => 'ioan.popescu@example.com',   'name' => 'Ioan Popescu',  'bio' => 'Pasionat de fotografie. Vand echipament pe care nu il mai folosesc.', 'city' => 'Timisoara'],
        ['email' => 'maria.ionescu@example.com',  'name' => 'Maria Ionescu', 'bio' => 'Inchiriez apartamente in zona centrala. Contract si acte in regula.', 'city' => 'Brasov'],
        ['email' => 'radu.stanescu@example.com',  'name' => 'Radu Stanescu', 'bio' => 'Colectionar de tablouri si obiecte de decor.', 'city' => 'Sibiu'],
        ['email' => 'ana.dumitru@example.com',    'name' => 'Ana Dumitru',   'bio' => 'Vand din casa lucruri in stare buna. Predare personala.', 'city' => 'Iasi'],
    ];

    /**
     * Anunturile. 'author' este indexul din USERS, 'images' sunt fisiere
     * care exista deja in public/uploads/ads/.
     */
    private const ADS = [
        [
            'title' => 'Aparat foto Canon EOS cu doua obiective',
            'description' => "Vand aparat foto Canon EOS folosit ocazional in ultimii doi ani.\n\nSe preda cu doua obiective (18-55mm si 50mm f/1.8), doua acumulatori, incarcator original si geanta de transport. Senzorul a fost curatat profesional acum trei luni.\n\nFunctioneaza impecabil, nu are zgarieturi pe lentile. Ideal pentru cineva care incepe cu fotografia.",
            'price' => 2400,
            'location' => ['Timisoara, Timis, Romania', 45.7489, 21.2087],
            'author' => 2,
            'daysAgo' => 1,
            'images' => [
                'camera0-6aa06be50da8f.jpg',
                'camera1-6aa06be50de5b.jpg',
                'camera2-6aa06be50e23a.jpg',
                'camera3-6aa06be50e551.jpg',
            ],
        ],
        [
            'title' => 'Garsoniera mobilata in zona centrala',
            'description' => "Inchiriez garsoniera de 32 mp situata la doi pasi de centru.\n\nApartamentul este complet mobilat si utilat: masina de spalat, frigider, aragaz, cuptor cu microunde. Are centrala proprie, geamuri termopan si aer conditionat.\n\nEtajul 2 din 4, bloc linistit, parcare in fata. Se cere garantie o luna. Nu se accepta animale de companie.",
            'price' => 1700,
            'location' => ['Brasov, Brasov, Romania', 45.6427, 25.5887],
            'author' => 3,
            'daysAgo' => 2,
            'images' => [
                'gars0-6aa070e6c1b4a.jpg',
                'gars1-6aa070e6c1f06.jpg',
                'gars2-6aa070e6c2211.jpg',
                'gars3-6aa070e6c250a.jpg',
                'gars4-6aa070e6c2877.jpg',
            ],
        ],
        [
            'title' => 'Samsung Galaxy S20 128GB, stare foarte buna',
            'description' => "Telefon Samsung Galaxy S20 cu 128GB spatiu de stocare, culoare gri.\n\nA fost tinut permanent in husa si cu folie pe ecran, deci nu are nicio zgarietura. Bateria tine o zi intreaga la utilizare normala. Acumulatorul nu a fost inlocuit.\n\nSe preda cu cutia originala, incarcator si cablu. Liber de retea.",
            'price' => 1450,
            'location' => ['Cluj-Napoca, Cluj, Romania', 46.7712, 23.6236],
            'author' => 1,
            'daysAgo' => 3,
            'images' => [
                's20-1-6aa08cbfc28a1.jpg',
                's20-2-6aa08cbfc2df2.jpg',
                's20-3-6aa08cbfc313a.jpg',
                's20-5-6aa08cbfc3911.jpg',
            ],
        ],
        [
            'title' => 'Monitor gaming 144Hz, 27 inch',
            'description' => "Monitor de gaming de 27 inch cu rata de refresh de 144Hz si timp de raspuns de 1ms.\n\nRezolutie Full HD, panou IPS, suport reglabil pe inaltime si pivotare. Are doua intrari HDMI si un DisplayPort. Fara pixeli morti.\n\nCumparat acum un an, il vand pentru ca am trecut pe un ultrawide. Cutia originala se pastreaza.",
            'price' => 950,
            'location' => ['Cluj-Napoca, Cluj, Romania', 46.7712, 23.6236],
            'author' => 1,
            'daysAgo' => 4,
            'images' => [
                'monitor0-6aa06f99774af.jpg',
                'monitor1-6aa06f9977a98.jpg',
                'monitor3-6aa06f9977e6b.jpg',
            ],
        ],
        [
            'title' => 'Mouse gaming wireless cu senzor optic',
            'description' => "Mouse wireless de gaming, senzor optic de 16000 DPI, sase butoane programabile.\n\nBateria tine aproximativ 60 de ore cu iluminarea oprita. Se incarca prin USB-C, cablul este inclus. Greutate 74 de grame.\n\nFolosit sase luni, alunecatoarele sunt intacte. Vine cu receiverul USB original.",
            'price' => 180,
            'location' => ['Bucuresti, Bucuresti, Romania', 44.4268, 26.1025],
            'author' => 0,
            'daysAgo' => 5,
            'images' => [
                'mouse0-6aa06dfab0c07.jpg',
                'mouse1-6aa06dfab0ff1.jpg',
                'mouse2-6aa06dfab13f9.jpg',
            ],
        ],
        [
            'title' => 'Set pixuri de colectie, 10 bucati',
            'description' => "Set de zece pixuri de colectie, majoritatea nefolosite.\n\nInclude modele cu corp metalic si cateva editii limitate. Toate scriu, am verificat fiecare in parte. Doua dintre ele au rezerve de schimb incluse.\n\nSe vand doar la pachet, nu desfac setul. Predare personala sau curier.",
            'price' => 220,
            'location' => ['Bucuresti, Bucuresti, Romania', 44.4268, 26.1025],
            'author' => 0,
            'daysAgo' => 6,
            'images' => [
                'pen1-6aa066e7478e1.jpg',
                'pen2-6aa066e747dba.jpg',
                'pen3-6aa066e748253.jpg',
                'pen4-6aa066e74869c.jpg',
            ],
        ],
        [
            'title' => 'Perdele transparente pentru living',
            'description' => "Vand perdele transparente, potrivite pentru living sau dormitor.\n\nLungime 2.8 metri, latime totala 5 metri. Material fin, se spala la masina la 30 de grade. Culoare crem deschis, se asorteaza usor.\n\nAu fost montate un an, sunt spalate si calcate. Nu au pete sau rupturi.",
            'price' => 300,
            'location' => ['Iasi, Iasi, Romania', 47.1585, 27.6014],
            'author' => 5,
            'daysAgo' => 7,
            'images' => [
                'perdele0-6aa06eb1ba354.jpg',
                'perdele1-6aa06eb1ba77b.jpg',
                'perdele2-6aa06eb1baa54.jpg',
            ],
        ],
        [
            'title' => 'Tablou pictat manual, ulei pe panza',
            'description' => "Tablou pictat manual in ulei pe panza, semnat de autor.\n\nDimensiuni 60x80 cm, cu rama din lemn masiv inclusa. Peisaj de munte in tonuri calde, se potriveste intr-un living sau birou.\n\nA stat in casa, ferit de lumina directa. Culorile sunt intacte. Se preda ambalat corespunzator.",
            'price' => 1250,
            'location' => ['Sibiu, Sibiu, Romania', 45.7983, 24.1256],
            'author' => 4,
            'daysAgo' => 8,
            'images' => [
                'pic0-6aa0717c77ff8.jpg',
                'pic1-6aa0717c783e8.jpg',
                'pic2-6aa0717c7870e.jpg',
            ],
        ],
        [
            'title' => 'Apartament 2 camere de inchiriat, semicentral',
            'description' => "Inchiriez apartament cu doua camere, 54 mp, in zona semicentrala.\n\nDecomandat, mobilat modern, utilat complet. Are balcon inchis cu termopan, centrala proprie si loc de parcare in curte. Etajul 3 din 4.\n\nAproape de statia de tramvai si de doua supermarketuri. Disponibil de la inceputul lunii viitoare.",
            'price' => 2100,
            'location' => ['Brasov, Brasov, Romania', 45.6427, 25.5887],
            'author' => 3,
            'daysAgo' => 10,
            'images' => [
                'gars5-6aa070e6c2b62.jpg',
                'camera10-6aa06be50f8f8.jpg',
            ],
        ],
        [
            'title' => 'Obiectiv foto 50mm f/1.8',
            'description' => "Obiectiv fix de 50mm cu deschidere f/1.8, montura Canon EF.\n\nExcelent pentru portrete si fotografie in lumina slaba. Focus automat rapid si silentios. Lentilele sunt curate, fara ciuperca sau zgarieturi.\n\nSe preda cu ambele capace si un filtru UV montat. Cumparat nou de la magazin, am factura.",
            'price' => 480,
            'location' => ['Timisoara, Timis, Romania', 45.7489, 21.2087],
            'author' => 2,
            'daysAgo' => 12,
            'images' => [
                'camera4-6aa06be50e873.jpg',
                'camera5-6aa06be50eb89.jpg',
            ],
        ],
        [
            'title' => 'Tablou abstract, dimensiuni mari',
            'description' => "Tablou abstract pictat in acrilic, dimensiuni 100x70 cm.\n\nCulori vii, potrivit pentru un perete liber intr-un spatiu modern. Rama simpla din lemn negru. Panza este intinsa corect pe sasiu.\n\nIl vand pentru ca m-am mutat intr-un apartament mai mic si nu mai am unde sa il pun.",
            'price' => 890,
            'location' => ['Sibiu, Sibiu, Romania', 45.7983, 24.1256],
            'author' => 4,
            'daysAgo' => 14,
            'images' => [
                'pic3-6aa0717c78ab4.jpg',
            ],
        ],
        [
            'title' => 'Set birou: mouse si accesorii',
            'description' => "Vand la pachet accesorii de birou ramase dupa ce mi-am schimbat setup-ul.\n\nInclude un mousepad mare, un suport de laptop din aluminiu si un hub USB cu patru porturi. Toate functioneaza si sunt in stare buna.\n\nPretul este pentru intreg pachetul. Nu desfac.",
            'price' => 260,
            'location' => ['Bucuresti, Bucuresti, Romania', 44.4268, 26.1025],
            'author' => 0,
            'daysAgo' => 16,
            'images' => [
                'mouse2-6aa06dfab13f9.jpg',
                'monitor1-6aa06f9977a98.jpg',
            ],
        ],
        [
            'title' => 'Perdele si draperii, set complet',
            'description' => "Set complet de perdele si draperii pentru o camera.\n\nDraperiile sunt din material gros, opac, ideale pentru dormitor. Perdelele transparente se pot folosi separat. Toate au fost curatate chimic.\n\nGaleria nu este inclusa. Predare in Iasi sau expediere prin curier pe cheltuiala cumparatorului.",
            'price' => 450,
            'location' => ['Iasi, Iasi, Romania', 47.1585, 27.6014],
            'author' => 5,
            'daysAgo' => 18,
            'images' => [
                'perdele3-6aa06eb1bad5e.jpg',
                'perdele4-6aa06eb1bb0aa.jpg',
                'perdele5-6aa06eb1bb403.jpg',
            ],
        ],
    ];

    /**
     * Cine ce a pus la favorite: indexul anuntului => lista de indecsi de useri.
     */
    private const FAVORITES = [
        0 => [0, 1, 3, 5],
        1 => [0, 2, 4],
        2 => [0, 3],
        3 => [1, 2, 5],
        4 => [3],
        7 => [0, 1, 2, 5],
        8 => [4, 5],
    ];

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        foreach (self::USERS as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, self::PASSWORD)
            );
            $user->setIsVerified(true);

            $profile = new UserProfile();
            $profile->setName($data['name']);
            $profile->setBio($data['bio']);
            $profile->setAddress($data['city']);
            $user->setUserProfile($profile);

            $manager->persist($user);
            $users[] = $user;
        }

        $ads = [];

        foreach (self::ADS as $data) {
            [$locationName, $latitude, $longitude] = $data['location'];

            $ad = new Advertisement();
            $ad->setTitle($data['title']);
            $ad->setDescription($data['description']);
            $ad->setPrice($data['price']);
            $ad->setAuthor($users[$data['author']]);
            $ad->setLocationName($locationName);
            $ad->setLatitude($latitude);
            $ad->setLongitude($longitude);
            $ad->setCreated(new DateTime(sprintf('-%d days', $data['daysAgo'])));

            foreach ($data['images'] as $position => $filename) {
                $image = new AdvertisementImage();
                $image->setFilename($filename);
                $image->setPosition($position);
                $ad->addImage($image);
            }

            $manager->persist($ad);
            $ads[] = $ad;
        }

        foreach (self::FAVORITES as $adIndex => $userIndexes) {
            foreach ($userIndexes as $userIndex) {
                $ads[$adIndex]->addFavoritedBy($users[$userIndex]);
            }
        }

        $manager->flush();
    }
}
