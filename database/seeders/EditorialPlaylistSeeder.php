<?php

namespace Database\Seeders;

use App\Models\EditorialPlaylist;
use Illuminate\Database\Seeder;

class EditorialPlaylistSeeder extends Seeder
{
    public function run(): void
    {
        $playlists = array_merge(
            $this->spotifyPlaylists(),
            $this->appleMusicPlaylists(),
            $this->amazonMusicPlaylists()
        );

        foreach ($playlists as $i => $p) {
            EditorialPlaylist::updateOrCreate(
                ['platform' => $p['platform'], 'name' => $p['name']],
                ['url' => $p['url'], 'sort_order' => $i, 'is_active' => true]
            );
        }
    }

    private function spotifyPlaylists(): array
    {
        return [
            ['platform' => 'Spotify', 'name' => 'EQUAL Italia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWUHxBb0SYtLj?si=gBnBM9VORja9uvXl_bHjVw&pi=kLE4-um3Smuxl'],
            ['platform' => 'Spotify', 'name' => 'Fresh Finds Italia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX0KBgD4Jf5tY?si=8lPJhEE3TqGtWwB2z4QsUw&pi=nV21DhsARp62v'],
            ['platform' => 'Spotify', 'name' => 'GENERAZIONE Z', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWYCIYGXn56uz?si=IC7VbLGEQXSnDXeSbDc8rQ&pi=Bfszka9xT96sN'],
            ['platform' => 'Spotify', 'name' => 'Indie Italia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX6PSDDh80gxI?si=Fj_sjbdWTKC3rVBsvgBQVA&pi=WBsds_5dTUOIJ'],
            ['platform' => 'Spotify', 'name' => 'Indie Triste', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWX21Ue9Rttn8?si=67Q75VxnQlSNd1hnW1TO8w&pi=vK5c0_UzTfK8V'],
            ['platform' => 'Spotify', 'name' => 'Indimenticabili', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX6ShdbyN9CkW?si=WocbpIlMQKqVADNKa6uBxA&pi=SCdbodN6RC2yz'],
            ['platform' => 'Spotify', 'name' => "Int'o Rione", 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWYrg01Xmlew6?si=A_0TZfc0TG-NpeZbWL4qoQ&pi=emHcqqfoQZiyj'],
            ['platform' => 'Spotify', 'name' => 'Napoli Centro', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX6gvUCtw1XOD?si=CMObif1GRCqV73KGk09GQg&pi=WI35Wse_QdG8e'],
            ['platform' => 'Spotify', 'name' => 'New Music Friday Italia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWVKDF4ycOESi?si=BNqgmAQSQkyUXv0w8s3DnQ&pi=IizFMfogQMmL1'],
            ['platform' => 'Spotify', 'name' => 'Novità Pop', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX6K3mlB5G3WG?si=ceCHmPbESfeSwyvUP78H2A&pi=AWoBhTIZRJGM7'],
            ['platform' => 'Spotify', 'name' => 'Novità Rap Italiano', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX1OQlaot30zi?si=3SdocN_jQUuPF5bGb2Yrpg&pi=brnsFoUuT0i14'],
            ['platform' => 'Spotify', 'name' => 'nuovo pop IT', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX2c7QgpQBJFr?si=hD4ANM8AQTO3iTM-jqTsnQ&pi=FRsZ_IhyTfW8a'],
            ['platform' => 'Spotify', 'name' => 'Pop italiano: video', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWZ5HtCdI1TCV?si=_UQAm7LKSCWn3KsKlJTw6g&pi=V3uB7X6_RM6hn'],
            ['platform' => 'Spotify', 'name' => 'Plus Ultra', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX14EWeH2Pwf3?si=pZyAbboyTlGlbxUVkZfTTA&pi=z4suWburQnqL-'],
            ['platform' => 'Spotify', 'name' => 'RADAR Italia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWVjDgOMO8jZl?si=QACiAK6FQXS_HNDBBaDaEA&pi=IHaNxhanRv6t6'],
            ['platform' => 'Spotify', 'name' => 'ragazzo triste', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX7JWqNxz28IX?si=PhquaegtSMuS74-a2IzqOQ&pi=UUgZJjxvSJGqW'],
            ['platform' => 'Spotify', 'name' => 'Raptopia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWUQru3jd69v5?si=MBKq8_SwT1ibXyygZIZDgw&pi=XQNSpvSURlim_'],
            ['platform' => 'Spotify', 'name' => 'Rock Italia', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWViUlcvfltyZ?si=_oGgw4gLSC2f9FDbMy2pMA&pi=kX9ED22VSay6p'],
            ['platform' => 'Spotify', 'name' => 'sanguegiovane', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWW9tK1GiTdMf?si=qUTN2JVtR7y0LyBn6HfTOw&pi=fdSHZ-FcQQmwV'],
            ['platform' => 'Spotify', 'name' => 'Scuola Indie', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DX6O5gXioqvYB?si=fHlwsQvWTjuxBqtu4gTEcA&pi=0pWW0jDUTQCnv'],
            ['platform' => 'Spotify', 'name' => 'Street Culto', 'url' => 'https://open.spotify.com/playlist/37i9dQZF1DWXU2naFUn37x?si=OlL3MYARTzquTS4AOf3tng&pi=RhvwIeEYTNKER'],
        ];
    }

    private function appleMusicPlaylists(): array
    {
        return [
            ['platform' => 'Apple Music', 'name' => 'OnRepeat', 'url' => 'https://music.apple.com/us/playlist/onrepeat/pl.426a1044619f47d6b1f86b3f79ecf857'],
            ['platform' => 'Apple Music', 'name' => 'A-List Pop', 'url' => 'https://music.apple.com/us/playlist/a-list-pop/pl.5ee8333dbe944d9f9151e97d92d1ead9'],
            ['platform' => 'Apple Music', 'name' => 'Alpha', 'url' => 'https://music.apple.com/us/playlist/alpha/pl.bcb2f44b6e194cfa8950a796b4e65cd1'],
            ['platform' => 'Apple Music', 'name' => 'ALT CTRL', 'url' => 'https://music.apple.com/us/playlist/alt-ctrl/pl.0b593f1142b84a50a2c1e7088b3fb683'],
            ['platform' => 'Apple Music', 'name' => 'High Maintenance', 'url' => 'https://music.apple.com/us/playlist/high-maintenance/pl.8573a92705fe4253af597147d7cb981f'],
            ['platform' => 'Apple Music', 'name' => 'New in Indie', 'url' => 'https://music.apple.com/us/playlist/new-in-indie/pl.dbc3a7bd6b4843cb830af1b7cbbadbd6'],
            ['platform' => 'Apple Music', 'name' => 'New Music Daily', 'url' => 'https://music.apple.com/us/playlist/new-music-daily/pl.2b0e6e332fdf4b7a91164da3162127b5'],
            ['platform' => 'Apple Music', 'name' => 'Puro Pop', 'url' => 'https://music.apple.com/us/playlist/puro-pop/pl.2754a2d6e0084e2b8106fcd35fef6492'],
            ['platform' => 'Apple Music', 'name' => 'R&B Rewind', 'url' => 'https://music.apple.com/us/playlist/r-b-rewind/pl.efaf877db72a4c05b2654eb4371d6c24'],
            ['platform' => 'Apple Music', 'name' => 'Rap Life', 'url' => 'https://music.apple.com/us/playlist/rap-life/pl.abe8ba42278f4ef490e3a9fc5ec8e8c5'],
            ['platform' => 'Apple Music', 'name' => 'Sexyy Red: Hood Summer', 'url' => 'https://music.apple.com/us/playlist/sexyy-red-hood-summer/pl.485634cb956b4366b989a7373e168ce7'],
            ['platform' => 'Apple Music', 'name' => 'Todays Hits', 'url' => 'https://music.apple.com/us/playlist/todays-hits/pl.f4d106fed2bd41149aaacabb233eb5eb'],
            ['platform' => 'Apple Music', 'name' => 'Viral Hits', 'url' => 'https://music.apple.com/us/playlist/viral-hits/pl.3de89e62aa3340038e08fa325c3f3f01'],
        ];
    }

    private function amazonMusicPlaylists(): array
    {
        return [
            ['platform' => 'Amazon Music', 'name' => 'Acoustic Chill', 'url' => 'https://music.amazon.it/playlists/B07ZG923HX?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_wkp1XwZOJrJvRWI7pHpfRlRdO'],
            ['platform' => 'Amazon Music', 'name' => 'All Hits', 'url' => 'https://music.amazon.it/playlists/B07QY219HM?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_OWKTE9enCValXLfEYyOGpw471'],
            ['platform' => 'Amazon Music', 'name' => 'Buona Giornata', 'url' => 'https://music.amazon.it/playlists/B076TDPYC1?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_LjAkfYd1FZISCwkqulgBdQWqk'],
            ['platform' => 'Amazon Music', 'name' => 'Cantautori Italiani', 'url' => 'https://music.amazon.it/playlists/B07CT4KQ8T?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_Dq8HOc8LQ6Zck7F3Sv1GQrAJO'],
            ['platform' => 'Amazon Music', 'name' => 'Eplosione Indie', 'url' => 'https://music.amazon.it/playlists/B074Q1DJ14?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_JGzbLfKqrHeZLXhp10s4il36Y'],
            ['platform' => 'Amazon Music', 'name' => 'Flow Italiano', 'url' => 'https://music.amazon.it/playlists/B07RG14PWR?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_bSgXzgKF5XHogA6Yy1jxJFUjO'],
            ['platform' => 'Amazon Music', 'name' => "La vita è pop!", 'url' => 'https://music.amazon.it/playlists/B07RYVSC2H?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_N17KtwBKqUDvj8FkGj57ZQzLR'],
            ['platform' => 'Amazon Music', 'name' => 'Metropolitalia', 'url' => 'https://music.amazon.it/playlists/B083P65WB4?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_2bFhdrGr1QDj5c8ZI8wA32Oyb'],
            ['platform' => 'Amazon Music', 'name' => 'Nectar', 'url' => 'https://music.amazon.it/playlists/B07G4LLBXM?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_ihNImkTXxdlqYKSTnxRp5y7UV'],
            ['platform' => 'Amazon Music', 'name' => 'Nuova Gen', 'url' => 'https://music.amazon.it/playlists/B091JDCVX6?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_hsJxGE6A1f45uMlWK7mjg7KSk'],
            ['platform' => 'Amazon Music', 'name' => 'Platino', 'url' => 'https://music.amazon.it/playlists/B07D9XQL74?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_eQVvA9y4stn0aUvS9WDYPLRqK'],
            ['platform' => 'Amazon Music', 'name' => 'Pop Culture', 'url' => 'https://music.amazon.it/playlists/B07H4ZPCX1?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_X5A1URhNdvwQXYJ1nbMEHpTat'],
            ['platform' => 'Amazon Music', 'name' => 'Rock Arena', 'url' => 'https://music.amazon.it/playlists/B085Z9Z27G?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_68TtDExn7nVI9rk8Pg4lYOKgT'],
            ['platform' => 'Amazon Music', 'name' => 'Top Hits Oggi', 'url' => 'https://music.amazon.it/playlists/B073PJ6QC4?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_bDSZ4Be22YfNfYKbLaxmw3qk0'],
        ];
    }
}
