<?php
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminTitle = 'Inquiries';
include_once __DIR__ . '/includes/header.php';

$inquiries = [];
try {
    $inquiries = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll();
} catch(Exception $e) {}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">Customer Inquiries</span>
        <span style="font-size:0.8125rem; color:#9ca3af; font-weight:600;"><?php echo count($inquiries); ?> total</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inquiries)): ?>
                <tr><td colspan="5" style="text-align:center; padding:3rem; color:#9ca3af;">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">📭</div>
                    No inquiries yet.
                </td></tr>
                <?php endif; ?>
                <?php foreach($inquiries as $i => $inq): ?>
                <tr>
                    <td style="color:#9ca3af; font-weight:700;"><?php echo $i+1; ?></td>
                    <td style="font-weight:700; color:#111827;"><?php echo htmlspecialchars($inq['name']); ?></td>
                    <td>
                        <a href="tel:<?php echo $inq['phone']; ?>" style="color:#ef233c; font-weight:700; text-decoration:none; font-size:0.875rem;">
                            <?php echo htmlspecialchars($inq['phone']); ?>
                        </a>
                    </td>
                    <td style="color:#6b7280; max-width:300px;"><?php echo htmlspecialchars($inq['message'] ?? '—'); ?></td>
                    <td style="color:#9ca3af; font-size:0.8125rem; white-space:nowrap;"><?php echo date('d M Y, h:i A', strtotime($inq['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
