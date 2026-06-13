<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../helper.php';

class SongHandler
{
    public static function list(int $artistId, int $page, int $perPage = 10): array
    {
        $page = $page ?? max(1, (int)($_GET['p'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetchOne("SELECT COUNT(*) as total FROM musics WHERE artist_id = ?", [$artistId])['total'];
        $lastPage = max(1, (int) ceil($total / $perPage));

        $data = Database::fetchAll(
            "SELECT m.* FROM musics m WHERE m.artist_id = ? ORDER BY m.created_at DESC LIMIT? OFFSET ?",
            [$artistId, $perPage, $offset]
        );

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'total' => $total,
            'has_prev' => $page > 1,
            'has_next' => $page < $lastPage,
            'prev_page' => $page - 1,
            'next_page' => $page + 1,
        ];
    }

    public static function listAll(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetchOne("SELECT COUNT(*) as total FROM musics")['total'];
        $lastPage = max(1, (int) ceil($total / $perPage));

        $data = Database::fetchAll(
            "SELECT m.*, u.first_name, u.last_name
             FROM musics m
             JOIN artists a ON m.artist_id = a.id
             JOIN users u ON a.user_id = u.id
             ORDER BY m.created_at DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'total' => $total,
            'has_prev' => $page > 1,
            'has_next' => $page < $lastPage,
            'prev_page' => $page - 1,
            'next_page' => $page + 1,
        ];
    }

    public static function create(array $data): array
    {
        $errors = self::validate($data);

        if (!empty($errors))
            return ['success' => false, 'errors' => $errors];

        Database::insert(
            "INSERT INTO musics (artist_id, title, album_name, genre) VALUES (?, ?, ?, ?)",
            [$data['artist_id'], $data['title'], $data['album_name'], $data['genre']]
        );

        return ['success' => true];
    }

    public static function update(int $id, array $data): array
    {
        $errors = self::validate($data);

        if (!empty($errors))
            return ['success' => false, 'errors' => $errors];

        Database::query(
            "UPDATE musics SET title = ?, album_name = ?, genre = ? WHERE id = ?",
            [$data['title'], $data['album_name'], $data['genre'], $id]
        );

        return ['success' => true];
    }


    public static function delete(int $id): array
    {
        $song = Database::fetchOne("SELECT id FROM musics WHERE id = ?", [$id]);

        if (!$song)
            return ['success' => false, 'message' => 'Song not found.'];

        Database::query("DELETE FROM musics WHERE id = ?", [$id]);

        return ['success' => true, 'message' => 'Song deleted successfully.'];
    }

    public static function getArtistIdForUser(int $userId): ?int
    {
        $artist = Database::fetchOne("SELECT id FROM artists WHERE user_id = ?", [$userId]);
        return $artist ? (int) $artist['id'] : null;
    }

    private static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['title']))
            $errors['title'] = 'Title is required.';

        if (empty($data['album_name']))
            $errors['album_name'] = 'Album name is required.';

        if (empty($data['genre']))
            $errors['genre'] = 'Genre is required.';

        $allowedGenres = ['rnb', 'country', 'clasic', 'rock', 'jazz'];
        if (!empty($data['genre']) && !in_array($data['genre'], $allowedGenres))
            $errors['genre'] = 'Invalid genre selected.';

        return $errors;
    }
}
