<?php

namespace VotrePseudo\CafeBlog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use VotrePseudo\CafeBlog\Models\Category;
use VotrePseudo\CafeBlog\Models\Comment;
use VotrePseudo\CafeBlog\Models\Post;
use VotrePseudo\CafeBlog\Models\Tag;

class CafeBlogSeeder extends Seeder
{
    public function run(): void
    {
        $userModel = config('cafe-blog.user_model');
        $adminCol  = config('cafe-blog.admin_column', 'is_admin');

        // Cree un admin de demo si aucun n'existe
        $admin = $userModel::firstOrCreate(
            ['email' => 'admin@cafeblog.com'],
            [
                'name'     => 'Admin Cafeotheque',
                'password' => Hash::make('password'),
                $adminCol  => true,
            ]
        );

        $categories = collect([
            ['name' => 'Origines',              'slug' => 'origines',     'description' => 'Histoire et geographie du cafe.'],
            ['name' => 'Preparation',           'slug' => 'preparation',  'description' => 'Methodes d extraction et recettes.'],
            ['name' => 'Degustation',           'slug' => 'degustation',  'description' => 'Cupping, aromes et notes de degustation.'],
            ['name' => 'Materiel',              'slug' => 'materiel',     'description' => 'Machines, moulins et accessoires.'],
            ['name' => 'Torrefaction',          'slug' => 'torrefaction', 'description' => 'Du grain vert a la torrefaction artisanale.'],
            ['name' => 'Culture cafe',          'slug' => 'culture',      'description' => 'Le cafe et son role social et artistique.'],
        ])->map(fn ($c) => Category::firstOrCreate(['slug' => $c['slug']], $c));

        $tagNames = [
            'espresso', 'v60', 'chemex', 'aeropress', 'cold-brew',
            'arabica', 'robusta', 'ethiopie', 'colombie', 'bresil', 'kenya',
            'single-origin', 'specialty', 'filtre', 'immersion',
        ];
        $tags = collect($tagNames)->map(fn ($n) => Tag::firstOrCreate(['slug' => $n], ['name' => $n]));

        $postsData = [
            [
                'title'       => "L'Ethiopie, berceau du cafe : voyage dans la region de Yirgacheffe",
                'slug'        => 'ethiopie-yirgacheffe-berceau-cafe',
                'excerpt'     => "Decouvrez comment une province montagneuse d'Ethiopie a facon le cafe tel que nous le connaissons aujourd'hui.",
                'body'        => "<p>La region de Yirgacheffe, perchee a plus de 1 800 metres d'altitude, est unanimement reconnue comme l'une des origines les plus singulieres du monde du cafe specialty. Ses grains offrent une complexite aromatique hors du commun : fleurs blanches, bergamote, the vert et une acidite lumineuse.</p><p>La recolte, entierement manuelle, a lieu entre novembre et janvier. Les cerises rouges sont selectionnees une a une, puis generalement traitees par voie naturelle.</p>",
                'category_id' => $categories[0]->id,
                'tag_ids'     => [$tags->firstWhere('name', 'arabica')->id, $tags->firstWhere('name', 'ethiopie')->id, $tags->firstWhere('name', 'single-origin')->id],
            ],
            [
                'title'       => "Maitriser l'espresso : les parametres cles d'une extraction parfaite",
                'slug'        => 'maitriser-espresso-extraction-parfaite',
                'excerpt'     => "Dose, mouture, temperature, pression : tout ce que vous devez regler pour un espresso remarquable.",
                'body'        => "<p>L'espresso reste la reference absolue en matiere de cafe concentre. Sa preparation exige rigueur et repetabilite. Visez un temps d'extraction de 25 a 30 secondes pour un ratio 1:2.</p><p>La mouture est le parametre le plus sensible. Trop fine, elle ralentit l'extraction et genere de l'amertume. Trop grossiere, elle donne un espresso plat et acide.</p>",
                'category_id' => $categories[1]->id,
                'tag_ids'     => [$tags->firstWhere('name', 'espresso')->id, $tags->firstWhere('name', 'specialty')->id],
            ],
            [
                'title'       => "V60 vs Chemex : quelle methode filtre choisir ?",
                'slug'        => 'v60-vs-chemex-methode-filtre',
                'excerpt'     => "Deux icones du cafe filtre face a face. Leurs differences pour choisir selon votre palais.",
                'body'        => "<p>Le V60 de Hario et le Chemex de Peter Schlumbohm partagent le principe de la percolation par gravite, mais produisent des profils de tasse sensiblement differents.</p><p>Le V60 donne un cafe vif, expressif, qui met en valeur l'acidite. Le Chemex, avec son filtre plus epais, produit une tasse plus propre et plus douce.</p>",
                'category_id' => $categories[1]->id,
                'tag_ids'     => [$tags->firstWhere('name', 'v60')->id, $tags->firstWhere('name', 'chemex')->id, $tags->firstWhere('name', 'filtre')->id],
            ],
        ];

        foreach ($postsData as $data) {
            $tagIds = $data['tag_ids'];
            unset($data['tag_ids']);

            $post = Post::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'user_id'      => $admin->id,
                    'published_at' => now()->subDays(rand(1, 30)),
                ])
            );

            $post->tags()->syncWithoutDetaching($tagIds);

            Comment::firstOrCreate(
                ['post_id' => $post->id, 'guest_email' => 'demo@cafeblog.com'],
                [
                    'guest_name' => 'Marie Dupont',
                    'body'       => 'Excellent article, tres instructif !',
                    'approved'   => true,
                ]
            );
        }
    }
}
