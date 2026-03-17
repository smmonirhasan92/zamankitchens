<?php
ob_start();
require_once __DIR__ . '/../includes/db.php';

// Handle Add/Edit/Delete Generic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gen_name'])) {
    $name = trim($_POST['gen_name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $id = $_POST['gen_id'] ?? null;
    $indication = $_POST['indication'] ?? '';
    $side_effects = $_POST['side_effects'] ?? '';

    if (!empty($name)) {
        try {
            if (!empty($id)) {
                $pdo->prepare("UPDATE generics SET name=?, slug=?, indication=?, side_effects=? WHERE id=?")
                    ->execute([$name, $slug, $indication, $side_effects, $id]);
                $msg = "Generic name updated successfully!";
            } else {
                $pdo->prepare("INSERT INTO generics (name, slug, indication, side_effects) VALUES (?, ?, ?, ?)")
                    ->execute([$name, $slug, $indication, $side_effects]);
                $msg = "Generic name added successfully!";
            }
            header("Location: generics.php?msg=" . urlencode($msg));
            exit();
        } catch(Exception $e) {
            header("Location: generics.php?error=" . urlencode("Error: " . $e->getMessage()));
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM generics WHERE id=?")->execute([$_GET['delete']]);
        header("Location: generics.php?msg=Generic deleted successfully!");
        exit();
    } catch(Exception $e) {
        header("Location: generics.php?error=Cannot delete generic. It may be linked to products.");
        exit();
    }
}

$adminTitle = 'Manage Generics';
include_once __DIR__ . '/includes/header.php';

$generics = [];
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
try {
    $generics = $pdo->query("SELECT * FROM generics ORDER BY name ASC")->fetchAll();
} catch(Exception $e) {}
?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Pharmaceutical Generics</h1>
            <p class="text-sm text-slate-500">Manage generic names for pharmaceutical products.</p>
        </div>
    </div>

    <?php if($msg): ?> <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-6 font-bold border border-emerald-100 flex items-center gap-3"><i class="ph ph-check-circle text-xl"></i> <?php echo htmlspecialchars($msg); ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-6 font-bold border border-rose-100 flex items-center gap-3"><i class="ph ph-warning-circle text-xl"></i> <?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem; align-items: start;">
        <!-- Add/Edit Form -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"><?php echo isset($_GET['edit']) ? 'Edit Generic' : 'Add New Generic'; ?></span>
            </div>
            <div class="admin-card-body">
                <form method="POST">
                    <?php 
                    $editGen = null;
                    if(isset($_GET['edit'])) {
                        foreach($generics as $g) if($g['id'] == $_GET['edit']) $editGen = $g;
                    }
                    ?>
                    <input type="hidden" name="gen_id" value="<?php echo $editGen['id'] ?? ''; ?>">
                    
                    <div style="margin-bottom:1.25rem;">
                        <label class="admin-label">Generic Name</label>
                        <input type="text" name="gen_name" required class="admin-input" placeholder="e.g. Paracetamol" value="<?php echo htmlspecialchars($editGen['name'] ?? ''); ?>">
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label class="admin-label">Indication</label>
                        <textarea name="indication" rows="3" class="admin-input" placeholder="What is it used for?"><?php echo htmlspecialchars($editGen['indication'] ?? ''); ?></textarea>
                    </div>

                    <div style="margin-bottom:1.5rem;">
                        <label class="admin-label">Side Effects</label>
                        <textarea name="side_effects" rows="3" class="admin-input" placeholder="Potential side effects..."><?php echo htmlspecialchars($editGen['side_effects'] ?? ''); ?></textarea>
                    </div>

                    <div style="display:flex; gap:0.75rem;">
                        <button type="submit" class="btn btn-primary" style="flex:1;"><?php echo $editGen ? 'Update Generic' : 'Add Generic'; ?></button>
                        <?php if($editGen): ?>
                            <a href="generics.php" class="btn btn-ghost">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Generics List -->
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">All Generics</span>
                <span style="font-size:0.75rem; color:#9ca3af; font-weight:700;"><?php echo count($generics); ?> Generics</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Generic Name</th>
                            <th>Slug</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($generics)): ?>
                        <tr><td colspan="4" style="text-align:center; padding:3rem; color:#9ca3af;">No generics found.</td></tr>
                        <?php endif; ?>
                        <?php foreach($generics as $i => $g): ?>
                        <tr>
                            <td style="color:#d1d5db; font-weight:700;"><?php echo $i+1; ?></td>
                            <td>
                                <div style="font-weight:800; color:#111827;"><?php echo htmlspecialchars($g['name']); ?></div>
                            </td>
                            <td>
                                <div style="font-family:monospace; font-size:0.6875rem; color:#9ca3af;">/<?php echo $g['slug']; ?></div>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:0.4rem;">
                                    <a href="generics.php?edit=<?php echo $g['id']; ?>" class="btn btn-ghost" style="padding:0.35rem 0.75rem; font-size:0.75rem; font-weight:700;">Edit</a>
                                    <button onclick="if(confirm('Delete this generic?')) window.location.href='generics.php?delete=<?php echo $g['id']; ?>'" class="btn btn-danger" style="padding:0.35rem 0.75rem; font-size:0.75rem; font-weight:700;">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
