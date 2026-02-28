<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\WishlistItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Editors ---
        $editors = [];
        $editorData = [
            'CD Projekt Red',
            'Rockstar Games',
            'Valve Corporation',
            'Epic Games',
            'PUBG Corporation',
            'Toby Fox',
            'InnerSloth',
            'Mojang',
            'Bethesda Game Studios',
            'Riot Games',
            'Blizzard Entertainment',
            'Supercell',
        ];
        foreach ($editorData as $name) {
            $editor = new Editor();
            $editor->setName($name);
            $manager->persist($editor);
            $editors[$name] = $editor;
        }

        // --- Genres ---
        $genres = [];
        $genreNames = ['Action', 'Adventure', 'RPG', 'Shooter', 'Puzzle', 'Strategy', 'Sports', 'Simulation', 'Horror', 'Racing'];
        foreach ($genreNames as $name) {
            $genre = new Genre();
            $genre->setName($name);
            $manager->persist($genre);
            $genres[$name] = $genre;
        }

        // --- Users ---
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@allgames.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@allgames.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // --- Games ---
        $gamesData = [
            [
                'name' => 'Cyberpunk 2077',
                'description' => 'Cyberpunk 2077 is an open-world, action-adventure story set in Night City, a megalopolis of the future. Players take on the role of V, a mercenary outlaw going after a one-of-a-kind implant that is the key to immortality.',
                'releaseDate' => '2020-12-10',
                'editor' => 'CD Projekt Red',
                'genres' => ['Action', 'RPG'],
                'imageName' => 'cyberpunk.jpg',
            ],
            [
                'name' => 'Red Dead Redemption 2',
                'description' => 'Red Dead Redemption 2 is an epic tale of life in America\'s unforgiving heartland. The game\'s vast and atmospheric world also provides the foundation for a brand new online multiplayer experience.',
                'releaseDate' => '2018-10-26',
                'editor' => 'Rockstar Games',
                'genres' => ['Action', 'Adventure'],
                'imageName' => null,
            ],
            [
                'name' => 'The Witcher 3: Wild Hunt',
                'description' => 'The Witcher 3: Wild Hunt is a story-driven open world RPG set in a visually stunning fantasy universe full of meaningful choices and impactful consequences.',
                'releaseDate' => '2015-05-19',
                'editor' => 'CD Projekt Red',
                'genres' => ['RPG', 'Action'],
                'imageName' => null,
            ],
            [
                'name' => 'GTA V',
                'description' => 'Grand Theft Auto V is a sprawling crime epic that takes players through the criminal underbelly of Los Santos.',
                'releaseDate' => '2013-09-17',
                'editor' => 'Rockstar Games',
                'genres' => ['Action', 'Adventure'],
                'imageName' => null,
            ],
            [
                'name' => 'Fortnite',
                'description' => 'Fortnite is a free-to-play Battle Royale game and so much more. Hang out peacefully with friends while watching a concert or movie. Build and create your own island, or fight to be the last one standing.',
                'releaseDate' => '2017-07-25',
                'editor' => 'Epic Games',
                'genres' => ['Shooter', 'Action'],
                'imageName' => null,
            ],
            [
                'name' => 'Minecraft',
                'description' => 'Minecraft is a sandbox game where players explore a blocky, procedurally generated 3D world with infinite terrain.',
                'releaseDate' => '2011-11-18',
                'editor' => 'Mojang',
                'genres' => ['Simulation', 'Adventure'],
                'imageName' => null,
            ],
            [
                'name' => 'Among Us',
                'description' => 'Among Us is a multiplayer game where crewmates try to complete tasks on a spaceship while impostors attempt to sabotage the mission and eliminate the crew.',
                'releaseDate' => '2018-06-15',
                'editor' => 'InnerSloth',
                'genres' => ['Strategy', 'Puzzle'],
                'imageName' => null,
            ],
            [
                'name' => 'PUBG',
                'description' => 'PlayerUnknown\'s Battlegrounds is a battle royale shooter that pits 100 players against each other in a struggle for survival.',
                'releaseDate' => '2017-12-20',
                'editor' => 'PUBG Corporation',
                'genres' => ['Shooter', 'Action'],
                'imageName' => null,
            ],
            [
                'name' => 'Undertale',
                'description' => 'Undertale is a role-playing game set in the Underground, a world of monsters who have been sealed below the surface by a magic barrier.',
                'releaseDate' => '2015-09-15',
                'editor' => 'Toby Fox',
                'genres' => ['RPG', 'Adventure'],
                'imageName' => null,
            ],
            [
                'name' => 'Half-Life 2',
                'description' => 'Half-Life 2 is a first-person shooter game set in the dystopian City 17. Players take the role of Gordon Freeman who must fight back against the alien Combine.',
                'releaseDate' => '2004-11-16',
                'editor' => 'Valve Corporation',
                'genres' => ['Shooter', 'Action'],
                'imageName' => null,
            ],
            [
                'name' => 'The Elder Scrolls V: Skyrim',
                'description' => 'Skyrim is a massive open-world action RPG where you play as the Dragonborn, a prophesied hero who must defeat the world-eating dragon Alduin.',
                'releaseDate' => '2011-11-11',
                'editor' => 'Bethesda Game Studios',
                'genres' => ['RPG', 'Action', 'Adventure'],
                'imageName' => null,
            ],
            [
                'name' => 'Rocket League',
                'description' => 'Rocket League is a high-powered hybrid of arcade-style soccer and vehicular mayhem with easy-to-understand controls and fluid, physics-driven competition.',
                'releaseDate' => '2015-07-07',
                'editor' => 'PUBG Corporation',
                'genres' => ['Sports', 'Action'],
                'imageName' => 'rocket_league.jpg',
            ],
        ];

        $gameObjects = [];
        foreach ($gamesData as $data) {
            $game = new Game();
            $game->setName($data['name']);
            $game->setDescription($data['description']);
            $game->setReleaseDate(new \DateTimeImmutable($data['releaseDate']));
            if (isset($editors[$data['editor']])) {
                $game->setEditor($editors[$data['editor']]);
            }
            foreach ($data['genres'] as $genreName) {
                if (isset($genres[$genreName])) {
                    $game->addGenre($genres[$genreName]);
                }
            }
            $manager->persist($game);
            $gameObjects[$data['name']] = $game;
        }

        $manager->flush();

        // --- Wishlists ---
        $wishlistGames = ['Cyberpunk 2077', 'Rocket League', 'The Witcher 3: Wild Hunt'];
        foreach ($wishlistGames as $gameName) {
            if (isset($gameObjects[$gameName])) {
                $item = new WishlistItem();
                $item->setUser($user);
                $item->setGame($gameObjects[$gameName]);
                $manager->persist($item);
            }
        }

        $adminWishlist = new WishlistItem();
        $adminWishlist->setUser($admin);
        $adminWishlist->setGame($gameObjects['Cyberpunk 2077']);
        $manager->persist($adminWishlist);

        // --- Reviews ---
        $reviewData = [
            ['game' => 'Cyberpunk 2077', 'user' => $user, 'review' => true, 'comment' => 'Amazing game, incredible open world!'],
            ['game' => 'The Witcher 3: Wild Hunt', 'user' => $user, 'review' => true, 'comment' => 'Best RPG ever made.'],
            ['game' => 'Cyberpunk 2077', 'user' => $admin, 'review' => true, 'comment' => 'Visually stunning after all the patches.'],
            ['game' => 'Fortnite', 'user' => $admin, 'review' => false, 'comment' => 'Not my type of game.'],
        ];

        foreach ($reviewData as $rd) {
            if (isset($gameObjects[$rd['game']])) {
                $review = new Review();
                $review->setUser($rd['user']);
                $review->setGame($gameObjects[$rd['game']]);
                $review->setReview($rd['review']);
                $review->setComment($rd['comment']);
                $manager->persist($review);
            }
        }

        $manager->flush();
    }
}
