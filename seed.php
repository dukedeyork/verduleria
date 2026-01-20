<?php
require_once 'db.php';

try {
    $pdo->exec("USE verduleria");

    // Disable foreign key checks to allow truncation
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE products");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Re-seed Categories
    $categories = ['Frutas', 'Verduras', 'Orgánico'];
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    foreach ($categories as $cat) {
        $stmt->execute([$cat]);
    }

    // Get Category IDs
    $stmt = $pdo->query("SELECT id, name FROM categories");
    $cats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ['Frutas' => 1, 'Verduras' => 2, ...]

    // Fallback if cats are missing (shouldn't happen if install ran)
    $frutasId = $cats['Frutas'] ?? 1;
    $verdurasId = $cats['Verduras'] ?? 2;

    $products = [
        [
            'name' => 'Manzanas Rojas',
            'price' => 2.50,
            'category_id' => $frutasId,
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDTvf6rIcxbXHlONC2_cGXVVhuxm8LqUn5SkQY44hjL6Ox6ZfiFsfxSAX3cXUm1uYcFkHB3200tsATPmJ8ZRnT_WvpeOvMPHgF4S4l0fBtSJwVI3Zpy1vMn0c_n7oQzRc9GidxBEFj4JoHqI7oDjHk1K5Dbg2RJE5sF-OOySP9hlXPgXXzcW9wjiKpHCh2sUAFEoNfZ0naJphYm2UBPn88cdhoUMv3Hdj6-Drs6vkuQQCxM1O601BUocLEUt1QcISXkBHV1FPt8iAU0',
            'description' => 'Manzanas rojas frescas y jugosas.'
        ],
        [
            'name' => 'Zanahorias',
            'price' => 1.80,
            'category_id' => $verdurasId,
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBVPtxFufcKfVUMoek1Iho0kpz5q1TudOJCm99tZLk8cH_8MZE-vdZwQdpoekMNTTvGY2w7sjWn1CWcQ0E_ytWcqd88iaiyBoWlr9eLsnR4FFpF_eFq8E-rcmpaICPkyTg-TbkkJ1-xQ81lVwID3J4SCTBuP1e2JkPbfYeNsSZQyGNd92ZUQdVTaRS1fbIt2NGalpyl6hlmMIL5Muw5dtdyMF_d6eLiQi5Kkij4D4mJv6a_kFF-ZLiqFY6ZMUfGWbgkI3r9q5rneVeb',
            'description' => 'Zanahorias orgánicas ricas en vitamina A.'
        ],
        [
            'name' => 'Plátanos',
            'price' => 1.20,
            'category_id' => $frutasId,
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBIUzZhytxeYh-zWLCskPiK4hg1N5egR5oNjQcouPQ-CEEwJWLGsldX_m1ekwqflXB598453BktmswbIoQi_3gC2SDvREV7VEigBj49PUKbaOlittq3uG7XzA99MUf4bEbrNmcPC5Oy7oiL8u5LKcZ57nL-Epdo4gUPz4jjb1ymTVX9PJDFRUn4LjRsFr_7G8rXwMQx3qJssA3ZJrv2c2axo9ga-16GYAvhPUqvZ3keB_cqN4J80F8iJ1Nimu8Pa5X_44IPwARI4jF9',
            'description' => 'Plátanos dulces, perfectos para el desayuno.'
        ],
        [
            'name' => 'Brócoli',
            'price' => 3.00,
            'category_id' => $verdurasId,
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDrV3lmnwZ_UOU7nyGdYd0zoUgOzunU8FgThwX6WQ7ns5cyXPgI1oEqrCdKurC08jojKnewv2LW40cOq8MR-5aDOvte6r2hyEWLqCkP2ZaMzq7bJjau7El7vFfzKcsp7-NomtaARG-DVxMltY5BkvSSeiF0g0wRaEPbLa9EpSEJ888wRxkqPYRY4mWIvUhTp5LWtysj1fqqp5Bet5UuvfkWwNaQicnNbP-pxaG6EnNtvkGahg7uO8Qnl0T7J1Ng5xYr_nmiWOBold4_',
            'description' => 'Brócoli verde fresco.'
        ],
        [
            'name' => 'Fresas',
            'price' => 4.50,
            'category_id' => $frutasId,
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNFF6nSGccO--9UXUBax0ZQ-eRda90zA6jw5PC90qlThutUFXOMesDhLKLtLuzgm66L9zfwVyqNxiXeLRTc5bGFyDAmAXfxk62rhzIO5rJu3exWLYQqnOS8K2qnC9wRSG0ZE9UIV_URS-s04OQykHZjxpWacudh3WYBP5S9esbPVSZP62YvmdcY37BuMcqRxGW1Z0A9KQPYsiDtts8JyHSmr4Z1A9WpINJFagG64mGlr6FR0PAGYnMgfuO2azsAlwCPNqksf5eu0e8',
            'description' => 'Fresas rojas y dulces de temporada.'
        ],
        [
            'name' => 'Espinacas',
            'price' => 2.20,
            'category_id' => $verdurasId,
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAa_i4Es3AmFuiYzAX17xcUJ2dKikU6-DssE3oTvUQinNKOOT-CGpJuNoX8KdUZ7rNGZDhb8jsG7Q3AEKDOljKjSFtDhh7U3olJ9L5-VP4B7TEAnTmyA2hCtzMgX3IOdatmtAPzk1PDZa5bYehCsskyRA0bPBK49w-r_PN_87qXIcmm9vQdn5I-rYu0TgkMVoMEUpaxohvilylCDjds3JUljWYAtKXYX_DPyJG3utpPzyU8q8OZwIO6sAB82eRvVE-eY3A0ksK5bFKq',
            'description' => 'Hojas de espinaca frescas.'
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image_url, stock) VALUES (?, ?, ?, ?, ?, 100)");

    foreach ($products as $p) {
        $stmt->execute([$p['name'], $p['description'], $p['price'], $p['category_id'], $p['image_url']]);
    }

    echo "Products seeded successfully!";

} catch (PDOException $e) {
    echo "Seed Error: " . $e->getMessage();
}
?>