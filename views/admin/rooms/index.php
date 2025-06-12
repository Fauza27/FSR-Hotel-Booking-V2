<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2>Daftar Kamar</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <form method="get" action="<?= base_url('admin/rooms') ?>" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor kamar..." 
                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
                
                <select name="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $category_item): ?>
                        <option value="<?php echo $category_item->category_id; ?>" 
                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category_item->category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category_item->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-primary">Filter</button>
                
                <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
                    <a href="<?= base_url('admin/rooms') ?>" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </form>
            
            <a href="<?= base_url('admin/rooms/create') ?>" class="btn btn-primary">+ Tambah Kamar</a>
        </div>
        
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <!-- <th>Gambar</th> -->
                    <th>Nomor Kamar</th>
                    <th>Kategori</th>
                    <th>Harga / Malam</th>
                    <th>Kapasitas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rooms)): ?>
                    <?php $no = ($page - 1) * $limit + 1; ?>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <!-- <td>
                                <?php
                                $displayImageUrl = null;
                                // Prioritas 1: Gambar utama dari room_images (jika ada)
                                if (!empty($room->primary_image_url)) {
                                    $displayImageUrl = base_url($room->primary_image_url);
                                }
                                // Prioritas 2: Gambar dari kolom image_url di tabel rooms (jika primary_image_url tidak ada)
                                elseif (!empty($room->image_url)) {
                                    $displayImageUrl = base_url($room->image_url);
                                }
                                // Prioritas 3: Placeholder jika tidak ada gambar sama sekali
                                else {
                                    $displayImageUrl = base_url('assets/images/room-placeholder.jpg'); // Pastikan path placeholder benar
                                }
                                ?>
                                <img src="<?= $displayImageUrl ?>" alt="Gambar Kamar <?= htmlspecialchars($room->room_number) ?>" style="width: 100px; height: 75px; object-fit: cover;">
                            </td> -->
                            <td><?php echo htmlspecialchars($room->room_number); ?></td>
                            <td><?php echo htmlspecialchars($room->category_name ?? ''); ?></td>
                            <td>Rp <?php echo number_format($room->price_per_night, 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($room->capacity); ?> org</td>
                            <td>
                                <span class="badge badge-<?php echo $room->status == 'available' ? 'success' : ($room->status == 'occupied' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($room->status)); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/rooms/view/' . (int)$room->room_id) ?>" class="btn btn-info btn-sm">View</a>
                                <a href="<?= base_url('admin/rooms/edit/' . (int)$room->room_id) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('admin/rooms/delete/' . (int)$room->room_id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kamar ini?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">Tidak ada data kamar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="pagination-nav">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <?php
                        $queryParams = [];
                        if (isset($_GET['search']) && $_GET['search'] !== '') $queryParams['search'] = $_GET['search'];
                        if (isset($_GET['category']) && $_GET['category'] !== '') $queryParams['category'] = $_GET['category'];
                        $queryParams['page'] = $i;
                        ?>
                        <a class="page-link" href="?<?= http_build_query($queryParams) ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>