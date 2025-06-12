<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    
    <div class="admin-content">
        <div class="dashboard-header">
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <?php echo $_SESSION['admin_username']; ?>!</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalRooms; ?></h3>
                    <p>Total Kamar</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalBookings; ?></h3>
                    <p>Total Booking</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total User</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pendingBookings; ?></h3>
                    <p>Booking Pending</p>
                </div>
            </div>
        </div>
        
        <!-- Charts and Tables -->
        <div class="dashboard-content">
            <div class="dashboard-row">
                <!-- Room Availability Chart -->
                <div class="dashboard-card">
                    <h3>Status Ketersediaan Kamar</h3>
                    <div class="chart-container" style="min-height: 300px; max-height: 350px;">
                        <canvas id="roomAvailabilityChart"></canvas>
                    </div>
                </div>
                
                <!-- Monthly Revenue Chart -->
                <div class="dashboard-card">
                    <h3>Pendapatan Bulanan</h3>
                    <div class="chart-container" style="min-height: 300px; max-height: 350px;">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bookings Table -->
            <div class="dashboard-card">
                <h3>Booking Terbaru</h3>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tamu</th>
                                <th>Kamar</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentBookings)): ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>#<?php echo $booking->booking_id; ?></td>
                                        <td><?php echo htmlspecialchars($booking->full_name); ?></td>
                                        <td><?php echo htmlspecialchars($booking->room_number); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking->check_in_date)); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking->check_out_date)); ?></td>
                                        <td>Rp <?php echo number_format($booking->total_price, 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $booking->status; ?>">
                                                <?php echo ucfirst($booking->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= APP_URL ?>/admin/booking/details/<?php echo $booking->booking_id; ?>" 
                                               class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada booking terbaru</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= APP_URL ?>/admin/bookings" class="btn btn-primary">Lihat Semua Booking</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Room Availability Chart
    const roomAvailabilityData = {
        available: <?php echo $roomAvailability['available'] ?? 0; ?>,
        occupied: <?php echo $roomAvailability['occupied'] ?? 0; ?>,
        maintenance: <?php echo $roomAvailability['maintenance'] ?? 0; ?>
    };

    if (roomAvailabilityData.available > 0 || roomAvailabilityData.occupied > 0 || roomAvailabilityData.maintenance > 0) {
        const roomCtx = document.getElementById('roomAvailabilityChart').getContext('2d');
        new Chart(roomCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tersedia', 'Terisi', 'Maintenance'],
                datasets: [{
                    data: [
                        roomAvailabilityData.available,
                        roomAvailabilityData.occupied,
                        roomAvailabilityData.maintenance
                    ],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else {
        const roomChartContainer = document.getElementById('roomAvailabilityChart').parentNode;
        if (roomChartContainer) {
            roomChartContainer.innerHTML = '<p class="text-center text-muted">Tidak ada data ketersediaan kamar.</p>';
        }
    }

    // Monthly Revenue Chart
    const monthlyRevenueRawData = <?= json_encode($monthlyRevenue ?? []) ?>;
    
    // Debugging di console browser:
    console.log('Monthly Revenue Data from PHP:', monthlyRevenueRawData);

    if (monthlyRevenueRawData && monthlyRevenueRawData.length > 0) {
        // JavaScript mengharapkan 'month' dan 'revenue', yang sudah sesuai dengan output query baru
        const labels = monthlyRevenueRawData.map(item => item.month); 
        const revenueData = monthlyRevenueRawData.map(item => parseFloat(item.revenue) || 0);

        const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: revenueData,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        ticks: {
                            // autoSkip: true, 
                            // maxTicksLimit: 12 
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else {
        const revenueChartContainer = document.getElementById('monthlyRevenueChart').parentNode;
        if (revenueChartContainer) {
            revenueChartContainer.innerHTML = '<p class="text-center text-muted">Tidak ada data pendapatan bulanan untuk ditampilkan.</p>';
        }
    }
});
</script>

<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>