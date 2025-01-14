<?php
// Koneksikan database disini
$conf = mysqli_connect("localhost", "root", "");
$countfolders = glob(__DIR__ . '/*', GLOB_ONLYDIR);
$totalFolders = count($countfolders);
$getfolders   = array_filter(glob(__DIR__ . '/*'), 'is_dir');

$getdatabase = $conf->query("SELECT * FROM information_schema.schemata ORDER BY SCHEMA_NAME ASC");
$countdatabase = $getdatabase->num_rows;

$response = file_get_contents("https://entrolopy.site/assets/theme.json");
$theme    = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="en" data-theme="retro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostpy - Manage your Localhost</title>
    <script src="https://kit.fontawesome.com/cc8eb8fa05.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap');

body {
    font-family: nunito;
    width: 100%;
    min-height: 100vh;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.header {
    margin-top: 10px;
    max-width: 600px;
    width: 100%;
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 30px;
}
.header form{
    width: 100%;
    padding: 10px;
    border-radius: 10px;
}
.header form #jdl{
    font-size: 18px;
    font-weight: 600;
}
.header form label{
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 10px;
}
.header form label input{
    field-sizing: content;
    outline: none;
    min-width: 50px;
    padding: 0 10px;
    max-width: 200px;
    border-radius: 7px;
    font-size: 17px;
    text-align: center;
    font-weight: 550;
}
.header form label button{
    padding: 3px 10px;
    margin-top: 5px;
    border-radius: 10px;
    font-weight: 600;
}

.tabel{
    max-width: 1050px;
    width: 100%;
    display: grid;
    grid-template-columns: repeat(2,1fr);
    margin-top: 30px;
    gap: 30px;
}
.tabel .val{
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: start;
}
.tabel .val form{
    padding: 10px;
    display: flex;
    align-items: center;
    border-radius: 10px;
}
.tabel .val form input{
    outline: none;
    background: none;
    padding: 5px;
    field-sizing: content;
    max-width: 200px;
    font-weight: 600;
}
.tabel .val form button{
    padding: 5px 10px;
    border-radius: 5px;
}
.tabel .val .overflow-x-auto{
    width: 100%;
    border-radius: 10px;
}

.pagination {
    margin: 20px 0;
    text-align: center;
}
.pagination button {
    margin: 0 5px;
    padding: 5px 10px;
    cursor: pointer;
}
.pagination button:disabled {
    cursor: not-allowed;
    background-color: #ddd;
}
</style>
<body>
    <form method="post" class="tema" style="display: flex;justify-content: right;width: 100%;gap: 10px;margin: -10px 100px 0 10px;align-items:center;">
        <span id="current-theme" style="display: none;">Retro</span>
        <i class="fa-solid fa-palette"></i>
        <select id="theme-selector" style="field-sizing: content;border-radius: 10px;background: #fff;outline: none;color: black;font-weight: 600;padding:3px 10px;">
            <option>Ubah Tema</option>
            <?php
            foreach ($theme['themes'] as $tema) {
                echo "<option value='$tema'>$tema</option>";
            }
            ?>
        </select>
    </form>
    <div class="header">
        <form class="data bg-base-300" method="post">
            <p id="jdl">Folder</p>
            <p id="sub">Jumlah: <strong><?= $totalFolders ?></strong> Folder</p>
            <label>
                <input type="text" name="foldername" placeholder="Nama Folder" id="folder-name-1">
                <button type="submit" name="createfolder" class="bg-base-200">Buat Folder Baru</button>
            </label>
        </form>

        <form class="data bg-base-300" method="post">
            <p id="jdl">Database</p>
            <p id="sub">Jumlah: <strong><?= $countdatabase ?></strong> Database</p>
            <label>
                <input type="text" name="databasename" placeholder="Nama Database" id="folder-name-1">
                <button class="bg-base-200" type="submit" name="createdatabase">Buat Database Baru</button>
            </label>
        </form>
    </div>

    <div class="tabel">
        <div class="val folder">
            <form class="bg-base-300" method="post">
                <label>
                    <i class="fa-solid fa-folder-open"></i>
                    <input type="text" name="foldername" placeholder="Cari folder">
                </label>
                <button class="bg-base-200" type="submit" name="searchfolder"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <?php
            if (isset($_POST['searchfolder'])) {
                $directoryPath = __DIR__ . '/';
                $foldernames   = $_POST['foldername'];
                if (is_dir($directoryPath)) {
                    $folders = array_filter(scandir($directoryPath), function ($item) use ($directoryPath) {
                        return is_dir($directoryPath . $item) && $item !== '.' && $item !== '..';
                    });
                    $getfolders = array_filter($folders, function ($folder) use ($foldernames) {
                        return stripos($folder, $foldernames) !== false;
                    });
                }
            }
            ?>

            <div class="overflow-x-auto bg-base-200">
                <table class="table">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Nama Folder</th>
                            <th align="center">Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($getfolders as $rowfolder) {
                            $name = basename($rowfolder);
                        ?>
                            <tr>
                                <th><?= $no++ ?></th>
                                <td><?= $name ?></td>
                                <td align="center">
                                    <a href="vscode://file/<?= __DIR__ . '/' . $name ?>" class="btn btn-info btn-xs"><i class="fa-solid fa-code"></i></a>
                                    <button class="btn btn-warning btn-xs" onclick="editfolder<?= $name ?>.showModal()"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <dialog id="editfolder<?= $name ?>" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <h3 class="text-lg font-bold">Ubah Nama Folder <?= $name ?>?</h3>
                                            <form method="post" style="width: 100%;display: flex;flex-direction: column;gap: 10px;align-items: center;">
                                                <input type="text" name="oldfoldername" style="display: none;" value="<?= $name ?>">
                                                <input type="text" name="newfoldername" placeholder="Nama Folder Baru">
                                                <button type="submit" name="editfolder" class="btn btn-warning btn-sm">Ubah Sekarang</button>
                                            </form>
                                        </div>
                                    </dialog>

                                    <button class="btn btn-error btn-xs" onclick="deletefolder<?= $name ?>.showModal()"><i class="fa-solid fa-trash-can"></i></button>
                                    <dialog id="deletefolder<?= $name ?>" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <h3 class="text-lg font-bold">Hapus Folder <?= $name ?>?</h3>
                                            <form method="post" style="width: 100%;display: flex;flex-direction: column;gap: 10px;align-items: center;">
                                                <button type="submit" name="deletefolder" value="<?= $name ?>" class="btn btn-error btn-sm">Hapus Sekarang</button>
                                            </form>
                                        </div>
                                    </dialog>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="val database" style="align-items: end;">
            <form class="bg-base-300" method="post">
                <label>
                    <i class="fa-solid fa-folder-open"></i>
                    <input type="text" name="dbname" placeholder="Cari folder">
                </label>
                <button type="submit" name="searchdbname" class="bg-base-200"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <?php
            if (isset($_POST['searchdbname'])) {
                $dbnames = $_POST['dbname'];
                $getdatabase = $conf->query("SELECT * FROM information_schema.schemata WHERE SCHEMA_NAME LIKE '%$dbnames%' ORDER BY SCHEMA_NAME ASC");
            }
            ?>

            <div class="overflow-x-auto bg-base-200">
                <table class="table">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Nama Database</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($rows = $getdatabase->fetch_object()) {
                        ?>
                            <tr>
                                <th><?= $no++ ?></th>
                                <td><?= $rows->SCHEMA_NAME ?></td>
                                <td align="center">
                                    <?php
                                    if ($rows->SCHEMA_NAME == 'sys' or $rows->SCHEMA_NAME == 'information_schema' or $rows->SCHEMA_NAME == 'performance_schema' or $rows->SCHEMA_NAME == 'mysql') {
                                    ?>
                                        <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=<?= $rows->SCHEMA_NAME ?>" class="btn btn-info btn-xs"><i class="fa-solid fa-database"></i></a>
                                    <?php
                                    } else {
                                    ?>
                                        <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=<?= $rows->SCHEMA_NAME ?>" class="btn btn-info btn-xs"><i class="fa-solid fa-database"></i></a>
                                        <button class="btn btn-warning btn-xs" onclick="editdatabase<?= $rows->SCHEMA_NAME ?>.showModal()"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <dialog id="editdatabase<?= $rows->SCHEMA_NAME ?>" class="modal">
                                            <div class="modal-box">
                                                <form method="dialog">
                                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                </form>
                                                <h3 class="text-lg font-bold">Ubah <?= $rows->SCHEMA_NAME ?>?</h3>
                                                <form method="post" style="width: 100%;display: flex;flex-direction: column;gap: 10px;align-items: center;">
                                                    <input type="text" name="olddatabasename" style="display: none;" value="<?= $rows->SCHEMA_NAME ?>">
                                                    <input type="text" name="newdatabasename" placeholder="Nama database Baru">
                                                    <button type="submit" name="editdatabase" class="btn btn-warning btn-sm">Ubah Sekarang</button>
                                                </form>
                                            </div>
                                        </dialog>

                                        <button class="btn btn-error btn-xs" onclick="deletedatabase<?= $rows->SCHEMA_NAME ?>.showModal()"><i class="fa-solid fa-trash-can"></i></button>
                                        <dialog id="deletedatabase<?= $rows->SCHEMA_NAME ?>" class="modal">
                                            <div class="modal-box">
                                                <form method="dialog">
                                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                </form>
                                                <h3 class="text-lg font-bold">Hapus Database Nama database?</h3>
                                                <form method="post" style="width: 100%;display: flex;flex-direction: column;gap: 10px;align-items: center;">
                                                    <button type="submit" name="deletedatabase" value="<?= $rows->SCHEMA_NAME ?>" class="btn btn-error btn-sm">Hapus Sekarang</button>
                                                </form>
                                            </div>
                                        </dialog>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['createdatabase'])) {
        $databasename = $_POST['databasename'];
        $create = $conf->query("CREATE DATABASE $databasename");
        if ($create) {
            echo "<script>window.alert('Database $databasename Berhasil Dibuat')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        } else {
            echo "<script>window.alert('Database $databasename Gagal Dibuat')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        }
    }
    if (isset($_POST['editdatabase'])) {
        $olddatabase = $_POST['olddatabasename'];
        $newdatabase = $_POST['newdatabasename'];

        $createDbQuery = "CREATE DATABASE $newdatabase";
        if ($conf->query($createDbQuery) === TRUE) {
            echo '';
        } else {
            echo '';
        }
        $tablesQuery = $conf->query("SHOW TABLES FROM $olddatabase");
        if ($tablesQuery === FALSE) {
            echo '';
        }

        while ($table = $tablesQuery->fetch_array()) {
            $tableName = $table[0];
            $createTableQuery = $conf->query("SHOW CREATE TABLE $olddatabase.$tableName");
            if ($createTableQuery === FALSE) {
                echo '';
            }

            $createTableSql = $createTableQuery->fetch_assoc()['Create Table'];
            $createTableSql = str_replace("`$tableName`", "`$newdatabase`.`$tableName`", $createTableSql);
            if ($conf->query($createTableSql) === TRUE) {
                echo '';
            } else {
                echo '';
            }
            $copyDataQuery = "INSERT INTO $newdatabase.$tableName SELECT * FROM $olddatabase.$tableName";
            if ($conf->query($copyDataQuery) === TRUE) {
                echo '';
            } else {
                echo '';
            }
        }
        $dropDbQuery = "DROP DATABASE $olddatabase";
        if ($conf->query($dropDbQuery) === TRUE) {
            echo "<script>window.alert('$olddatabase Berhasil Diganti Menjadi $newdatabase')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        } else {
            echo "<script>window.alert('$olddatabase Gagal Diganti Menjadi $newdatabase')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        }
    }

    if (isset($_POST['deletedatabase'])) {
        $database = $_POST['deletedatabase'];
        $delete = $conf->query("DROP DATABASE $database");
        if ($delete) {
            echo "<script>window.alert('$database Berhasil Dihapus')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        } else {
            echo "<script>window.alert('$database Gagal Dihapus')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        }
    }

    if (isset($_POST['createfolder'])) {
        $folderName = $_POST['foldername'];
        $path = __DIR__ . "/" . $folderName;
        if (!file_exists($path)) {
            if (mkdir($path, 0777, true)) {
                echo "<script>window.alert('Folder $folderName Berhasil Dibuat')</script>";
                echo "<meta http-equiv='refresh' content='0;'>";
            } else {
                echo "<script>window.alert('Folder $folderName Gagal Dibuat')</script>";
                echo "<meta http-equiv='refresh' content='0;'>";
            }
        } else {
            echo "<script>window.alert('Nama Folder $folderName Sudah Ada')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        }
    }

    if (isset($_POST['editfolder'])) {
        $oldfolder = $_POST['oldfoldername'];
        $newfolder = $_POST['newfoldername'];

        if (rename($oldfolder, $newfolder)) {
            echo "<script>window.alert('$oldfolder Berhasil Diubah Menjadi $newfolder')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        } else {
            echo "<script>window.alert('$oldfolder Gagal Diubah Menjadi $newfolder')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        }
    }

    if (isset($_POST['deletefolder'])) {
        $deletefolder = $_POST['deletefolder'];
        function deleteFolder($folderPath)
        {
            if (!is_dir($folderPath)) {
                echo "Path bukan folder atau tidak ditemukan.";
                return false;
            }
            $files = array_diff(scandir($folderPath), ['.', '..']);

            foreach ($files as $file) {
                $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    deleteFolder($filePath);
                } else {
                    unlink($filePath);
                }
            }
            return rmdir($folderPath);
        }
        if (deleteFolder($deletefolder)) {
            echo "<script>window.alert('Folder $deletefolder Berhasil Dihapus')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        } else {
            echo "<script>window.alert('Folder $deletefolder Gagal Dihapus')</script>";
            echo "<meta http-equiv='refresh' content='0;'>";
        }
    }
    ?>

    <script>
        const folderInputs = document.querySelectorAll('#folder-name-1, #folder-name-2, #folder-name-3');
        folderInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/ /g, '_');
            });
        });
    </script>
    <script>
        const themeSelector = document.getElementById('theme-selector');
        const htmlElement = document.documentElement;
        const currentThemeText = document.getElementById('current-theme');
        function setTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            currentThemeText.textContent = theme.charAt(0).toUpperCase() + theme.slice(1);
            themeSelector.value = theme;
            localStorage.setItem('selectedTheme', theme);
        }
        const savedTheme = localStorage.getItem('selectedTheme');
        if (savedTheme) {
            setTheme(savedTheme);
        } else {
            setTheme('retro');
        }
        themeSelector.addEventListener('change', function() {
            const selectedTheme = this.value;
            setTheme(selectedTheme);
        });
    </script>
</body>

</html>
