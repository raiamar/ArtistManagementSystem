<?php
require_once __DIR__ . '/../../../src/models/song.php';

$user = currenctUser();
$isArtist = $user['role'] === 'artist';
$artistId = isset($_GET['artist_id']) ? (int) $_GET['artist_id'] : null;

if ($isArtist) {
    $artistId = SongHandler::getArtistIdForUser($user['id']);
    if (!$artistId) {
        echo '<div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"><strong>Error:</strong> No artist profile found for your account.</div>';
        return;
    }
}


$page = max(1, (int)($_GET['p']  ?? 1));
$songs = $artistId ? SongHandler::list($artistId, $page) : SongHandler::listAll($page);
$sn = ($songs['current_page'] - 1) * $songs['per_page'];

$errors = [];
$old = $_POST;
$action = $_POST['action'] ?? '';
$isEdit = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Token is invalid.';
    } elseif ($action === 'create') {
        $_POST['artist_id'] = $artistId;
        $result = SongHandler::create($_POST);
        if ($result['success']) {
            $_SESSION['song_message'] = "Song created successfully.";
            header('Location: ?page=song'. ($artistId ? '&artist_id=' . $artistId : ''));
            exit;
        }
        $errors = $result['errors'];
    } elseif ($action === 'update') {
        $songId = (int) ($_POST['song_id'] ?? 0);

        if ($songId <= 0) {
            $errors['general'] = 'Invalid song.';
        } else {
            $result = SongHandler::update($songId, $_POST);
            if ($result['success']) {
                $_SESSION['song_message'] = "Song updated successfully.";
                header('Location: ?page=song' . ($artistId ? '&artist_id=' . $artistId : ''));
                exit;
            }
            $errors = $result['errors'];
            $isEdit = true;
        }
    } elseif ($action === 'delete') {
        $songId = (int) ($_POST['song_id'] ?? 0);
        if ($songId > 0) {
            $result = SongHandler::delete($songId);
            if ($result['success']) {
                $_SESSION['song_message'] = $result['message'];
            } else {
                $_SESSION['song_error'] = $result['message'];
            }
        }
        header('Location: ?page=song'. ($artistId ? '&artist_id=' . $artistId : ''));
        exit;
    }
}

$formMode = $isEdit ? 'update' : 'create';
$formSongId = $isEdit ? (int) ($_POST['song_id'] ?? 0) : 0;
?>

<?php
$successMessage = $_SESSION['song_message'] ?? null;
$deleteErrorMessage = $_SESSION['song_error'] ?? null;
if ($successMessage)
    unset($_SESSION['song_message']);
if ($deleteErrorMessage)
    unset($_SESSION['song_error']);
$hasErrors = !empty($errors);
?>

<header class="border-b border-solid border-gray-300 bg-white">
    <div class="flex justify-between items-center mb-4">
        <h2 class="p-6">Song Management</h2>

        <div class="flex gap-2">
            <?php if (hasRole('artist')): ?>
                <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition text-sm font-medium mr-4">+ New Song</button>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($successMessage): ?>
    <div class="mx-4 mt-4">
        <div id="success-alert" class="flex sm:items-center p-4 mb-4 text-sm rounded-md bg-green-200" role="alert">
            <svg class="w-4 h-4 shrink-0 mt-0.5 md:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div class="ms-2 text-sm"><?= htmlspecialchars($successMessage) ?></div>
            <button type="button" onclick="this.closest('#success-alert').remove();" class="ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg hover:bg-green-200 focus:ring-2 focus:ring-green-300 inline-flex h-8 w-8" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<?php if ($deleteErrorMessage): ?>
    <div class="mx-4 mt-4">
        <div id="error-alert" class="flex sm:items-center p-4 mb-4 text-sm rounded-md bg-red-200" role="alert">
            <svg class="w-4 h-4 shrink-0 mt-0.5 md:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div class="ms-2 text-sm"><?= htmlspecialchars($deleteErrorMessage) ?></div>
            <button type="button" onclick="document.getElementById('error-alert').remove();" class="ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg hover:bg-red-200 focus:ring-2 focus:ring-red-300 inline-flex h-8 w-8" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<section class="m-4 bg-white border border-gray-300 border-solid rounded shadow">
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3">S.N</th>
                    <?php if (!$artistId): ?>
                        <th class="px-6 py-3">Artist</th>
                    <?php endif; ?>
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Album</th>
                    <th class="px-6 py-3">Genre</th>
                    <th class="px-6 py-3">Created At</th>
                    <?php if (hasRole('artist')): ?>
                        <th class="px-6 py-3">Action</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($songs['data'] as $song): ?>
                    <?php $sn++; ?>
                    <tr class="bg-white border-b hover:bg-gray-50" data-song="<?= h(json_encode($song)) ?>">
                        <td class="px-6 py-4"><?= $sn ?></td>
                        <?php if (!$artistId): ?>
                            <td class="px-6 py-4 font-medium text-gray-900"><?= h($song['first_name'] . ' ' . $song['last_name']) ?></td>
                        <?php endif; ?>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= h($song['title']) ?></td>
                        <td class="px-6 py-4"><?= h($song['album_name']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                <?= match ($song['genre']) {
                                    'rnb' => 'bg-purple-100 text-purple-700',
                                    'country' => 'bg-yellow-100 text-yellow-700',
                                    'clasic' => 'bg-indigo-100 text-indigo-700',
                                    'rock' => 'bg-red-100 text-red-700',
                                    'jazz' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-700'
                                } ?>">
                                <?= ucfirst($song['genre']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4"><?= date('d M Y', strtotime($song['created_at'])) ?></td>
                        <?php if (hasRole('artist')): ?>
                            <td class="px-6 py-4">
                                <button onclick="editSong(this)" class="text-orange-600 hover:underline">Edit</button>
                                <button onclick="confirmDelete(this)" class="text-red-600 ml-3 hover:underline">Delete</button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach;  ?>
            </tbody>
        </table>
    </div>

    <div class="p-4">
        <?= paginationLinks($songs, '?page=song') ?>
    </div>
</section>


<?php if (hasRole('artist')): ?>
    <!-- Create/Update Modal -->
    <el-dialog>
        <dialog id="createSongModal" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
            <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

            <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
                <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-4xl data-closed:sm:translate-y-0 data-closed:sm:scale-95">

                    <h4 id="modal-title" class="text-xl font-bold text-center text-gray-800 mb-8">Manage Song</h4>

                    <?php if (!empty($errors['csrf'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?= h($errors['csrf']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?= h($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-3">
                        <form id="songForm" class="w-full" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" id="action" value="<?= $formMode ?>">
                            <input type="hidden" name="song_id" id="song_id" value="<?= $formSongId ?>">
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="mb-2 text-slate-900 font-medium text-sm inline-block">
                                        Title</label>
                                    <input type="text" id="title" name="title" value="<?= h($old['title'] ?? '') ?>" placeholder="Taal Ko Pani" required
                                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                                    <?php if (!empty($errors['title'])): ?>
                                        <p class="text-red-600 text-sm mt-1"><?= h($errors['title']) ?></p>
                                    <?php endif;  ?>
                                </div>
                                <div>
                                    <label for="album_name" class="mb-2 text-slate-900 font-medium text-sm inline-block">
                                        Album Name</label>
                                    <input type="text" id="album_name" name="album_name" value="<?= h($old['album_name'] ?? '') ?>" placeholder="Bheda Ko Oon Jasto" required
                                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                                    <?php if (!empty($errors['album_name'])): ?>
                                        <p class="text-red-600 text-sm mt-1"><?= h($errors['album_name']) ?></p>
                                    <?php endif;  ?>
                                </div>

                                <div>
                                    <label for="genre" class="mb-2 text-slate-900 font-medium text-sm inline-block">Genre</label>
                                    <select id="genre" name="genre" required
                                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600">
                                        <option value="" disabled <?= empty($old['genre']) ? 'selected' : '' ?>>Select genre</option>
                                        <?php $selectedGenre = $old['genre'] ?? ''; ?>
                                        <option value="rnb" <?= $selectedGenre === 'rnb' ? 'selected' : '' ?>>R&B</option>
                                        <option value="country" <?= $selectedGenre === 'country' ? 'selected' : '' ?>>Country</option>
                                        <option value="clasic" <?= $selectedGenre === 'clasic' ? 'selected' : '' ?>>Classic</option>
                                        <option value="rock" <?= $selectedGenre === 'rock' ? 'selected' : '' ?>>Rock</option>
                                        <option value="jazz" <?= $selectedGenre === 'jazz' ? 'selected' : '' ?>>Jazz</option>
                                    </select>
                                    <?php if (!empty($errors['gender'])): ?>
                                        <p class="text-red-600 text-sm mt-1"><?= h($errors['genre']) ?></p>
                                    <?php endif;  ?>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-4">

                                <button type="button" onclick="closeModal('createSongModal')" class="mt-3 inline-flex w-full justify-center rounded-md border-red-600 bg-red-600 px-3 py-2 text-white font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 sm:mt-0 sm:w-auto">Cancel</button>

                                <button id="submit-btn" type="submit"
                                    class="py-2 px-3.5 text-sm rounded-md font-semibold cursor-pointer tracking-wide text-white border border-blue-600 bg-blue-600 hover:bg-blue-700 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                    <?= $isEdit ? 'Update Song' : 'Create Song' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>

    <!-- Delete Confirmation Modal -->
    <el-dialog>
        <dialog id="deleteSongModal" aria-labelledby="delete-dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
            <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>
            <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
                <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-md data-closed:sm:translate-y-0 data-closed:sm:scale-95">
                    <h4 class="text-xl font-bold text-center text-gray-800 mb-4">Confirm Delete</h4>
                    <div class="p-3 text-center">
                        <p class="text-gray-600 mb-2">Are you sure you want to delete</p>
                        <p id="delete-song-title" class="text-lg font-semibold text-gray-900 mb-6"></p>
                        <form method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="song_id" id="delete-song_id" value="">
                            <div class="flex items-center justify-center gap-4">
                                <button type="button" onclick="closeModal('deleteSongModal')" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Cancel</button>
                                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                            </div>
                        </form>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>

<?php endif; ?>

<script>
    function closeModal(id) {
        document.getElementById(id).close();
    }

    function getSongData(btn) {
        return JSON.parse(btn.closest('tr').dataset.song);
    }

    function openCreateModal() {
        const modal = document.getElementById('createSongModal');

        document.getElementById('modal-title').textContent = 'Create Song';
        document.getElementById('submit-btn').textContent = 'Create Song';
        document.getElementById('action').value = 'create';
        document.getElementById('song_id').value = '';
        document.getElementById('songForm').reset();

        document.querySelectorAll('#createSongModal .text-red-600').forEach(function(el) {
            el.remove();
        });

        modal.showModal();
    }

    function editSong(btn) {
        
        const song = getSongData(btn);
        console.log(song);
        const modal = document.getElementById('createSongModal');
        document.getElementById('modal-title').textContent = 'Update Song';
        document.getElementById('submit-btn').textContent = 'Update Song';
        document.getElementById('action').value = 'update';
        document.getElementById('song_id').value = song.id;
        document.getElementById('title').value = song.title;
        document.getElementById('album_name').value = song.album_name;
        document.getElementById('genre').value = song.genre;

        document.querySelectorAll('#createSongModal .text-red-600').forEach(function(el) {
            el.remove();
        });

        modal.showModal();
    }

    function confirmDelete(btn) {
        const song = getSongData(btn);
        document.getElementById('delete-song-title').textContent = song.title;
        document.getElementById('delete-song_id').value = song.id;
        document.getElementById('deleteSongModal').showModal();
    }

    function openImportModal() {
        document.getElementById('importCsvModal').showModal();
    }

    <?php if ($hasErrors): ?>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($isEdit): ?>
                document.getElementById('modal-title').textContent = 'Update Artist';
                document.getElementById('submit-btn').textContent = 'Update Artist';
                document.getElementById('password').removeAttribute('required');
                document.getElementById('cpassword').removeAttribute('required');
            <?php endif; ?>
            document.getElementById('createArtistModel').showModal();
        });
    <?php endif; ?>
</script>