<!DOCTYPE html>
<html lang="en">
<?php

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
require "connection.php";
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FISAT WMS</title>
    <link rel="stylesheet" href="style.css"> 
    <script src="https://unpkg.com/@phosphor-icons/web"></script> 
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="app-container">
        <aside class="sidebar glass-panel">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="ph-fill ph-drop"></i>
                </div>
                <div>
                    <h2>FISAT WMS</h2>
                    <span class="brand-subtitle">Admin Portal</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <p class="nav-label">MAIN MENU</p>
                <a href="#" class="nav-item active" data-target="buildings-view">
                    <i class="ph ph-buildings"></i>
                    <span>Buildings</span>
                </a>
                <a href="#" class="nav-item" data-target="tanks-view">
                    <i class="ph ph-cylinder"></i>
                    <span>Water Tanks</span>
                </a>
                <a href="#" class="nav-item" data-target="motors-view">
                    <i class="ph ph-engine"></i>
                    <span>Motors / Pumps</span>
                </a>
                <a href="#" class="nav-item" data-target="usage-view">
                    <i class="ph ph-chart-line-up"></i>
                    <span>Water Usage</span>
                </a>
                <a href="#" class="nav-item" data-target="maintenance-view">
                    <i class="ph ph-wrench"></i>
                    <span>Maintenance</span>
                </a>
            </nav>
            <div class="sidebar-bottom">
                <a href="index.php" class="nav-item text-danger">
                    <i class="ph ph-sign-out"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <main class="main-content">
            <header class="topbar glass-panel">
                <div class="header-title">
                    <h1 id="page-title">Campus Buildings</h1>
                    <p id="page-subtitle" class="text-muted">Manage college buildings where water is supplied.</p>
                </div>
                <div class="user-profile">
                    <div class="notification-icon">
                        <i class="ph ph-bell"></i>
                        <?php
                        $notif_sql = "SELECT COUNT(MAINTENANCE_ID) AS pending_count FROM maintenance WHERE STATUS = 'Pending'";
                        $notif_result = mysqli_query($conn, $notif_sql);
                        $pending_alerts = 0;
                        if ($notif_result) {
                            $notif_row = mysqli_fetch_assoc($notif_result);
                            $pending_alerts = (int)$notif_row['pending_count'];
                        }
                        if ($pending_alerts > 0) {
                            echo '<span class="badge">' . $pending_alerts . '</span>';
                        }
                        ?>
                    </div>
                    <div class="avatar">
                        <i class="ph-fill ph-user"></i>
                    </div>
                    <div class="user-info">
                        <strong>Admin User</strong>
                        <span>System Admin</span>
                    </div>
                </div>
            </header>
            <div class="content-wrapper">
                <section id="buildings-view" class="view-section active">
                    <div class="card-grid">
                        <?php
                        $sql = "SELECT 
                            b.BUILDING_ID, 
                            b.BUILDING_NAME, 
                            b.LOCATION, 
                            (SELECT IFNULL(SUM(WATER_USED_LITERS), 0) FROM water_usage WHERE BUILDING_ID = b.BUILDING_ID) AS TOTAL_USAGE,
                            (SELECT COUNT(m.MAINTENANCE_ID) FROM maintenance m JOIN motor_pumps mp ON m.MOTOR_ID = mp.MOTOR_ID JOIN water_tanks wt ON mp.TANK_ID = wt.TANK_ID WHERE wt.BUILDING_ID = b.BUILDING_ID AND m.STATUS = 'Pending') AS PENDING_ISSUES
                        FROM buildings b;";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()){
                            $hasPending = $row['PENDING_ISSUES'] > 0;
                        ?>
                        <div class="data-card" style="position: relative; <?php echo $hasPending ? 'border: 1px solid #e74c3c;' : ''; ?>">
                            <?php if($hasPending): ?>
                            <div style="position: absolute; top: 15px; right: 15px; color: #e74c3c;" title="Pending Maintenance">
                                <i class="ph-fill ph-warning-circle" style="font-size: 1.5rem;"></i>
                            </div>
                            <?php endif; ?>
                            <div class="card-icon">
                                <i class="ph-fill ph-buildings"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($row['BUILDING_NAME']); ?></h3>
                            <div class="card-details">
                                <p><i class="ph ph-map-pin"></i> <?php echo htmlspecialchars($row['LOCATION']); ?></p>
                                <p><i class="ph ph-users"></i> ~<?php echo $row['BUILDING_ID'] ; ?> BUILDING ID</p> 
                                <p class="text-primary">
                                    <i class="ph ph-drop"></i> 
                                    <strong><?php echo number_format($row['TOTAL_USAGE']); ?> L</strong> Used
                                </p>
                                <?php if($hasPending): ?>
                                <p style="color: #e74c3c; font-size: 0.85em; font-weight: bold; margin-top: 5px;">
                                    <i class="ph ph-wrench"></i> <?php echo $row['PENDING_ISSUES']; ?> Motor Issue(s)!
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-actions">
                                <button class="btn-icon text-primary" title="Edit Building">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
                <section id="tanks-view" class="view-section">
                    <div class="section-toolbar">
                        <div class="search-box">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="tankSearch" placeholder="Search tanks...">
                        </div>
                        <button class="btn-primary" onclick="document.getElementById('addTankFormContainer').style.display = 'block';">
                            <i class="ph ph-plus"></i> Add Tank
                        </button>
                    </div>
                    <div class="form-card glass-panel mb-4" id="addTankFormContainer" style="display: none; margin-top: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3><i class="ph ph-plus-circle text-primary"></i> Register New Tank</h3>
                            <button onclick="document.getElementById('addTankFormContainer').style.display = 'none';" style="background:none; border:none; cursor:pointer; font-size:1.2rem; color: #e74c3c;">✖</button>
                        </div>
                        <form class="inline-form" id="addTankForm">
                            <input type="hidden" name="form_type" value="tank">
                            <div class="form-group">
                                <label>Tank ID</label>
                                <input type="text" name="tank_id" id="tank_id" placeholder="e.g. 101 or T-01" required>
                            </div>
                            <div class="form-group">
                                <label>Tank Name</label>
                                <input type="text" name="tank_name" id="tank_name" placeholder="e.g. Main Overhead Tank" required>
                            </div>
                            <div class="form-group">
                                <label>Capacity (Liters)</label>
                                <input type="number" name="capacity" id="capacity" placeholder="e.g. 10000" required>
                            </div>
                            <div class="form-group">
                                <label>Connected Building</label>
                                <select name="building_id" id="tank_building_id" required>
                                    <option value="">Select Building...</option>
                                    <?php
                                    $b_res = $conn->query("SELECT BUILDING_ID, BUILDING_NAME FROM buildings");
                                    while($b_row = $b_res->fetch_assoc()) {
                                        echo "<option value='".$b_row['BUILDING_ID']."'>".htmlspecialchars($b_row['BUILDING_NAME'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary mt-auto">Save Tank</button>
                            </div>
                        </form>
                        <div id="tankFeedback" style="margin-top: 10px; font-weight: 500;"></div>
                    </div>
                    <div class="table-container glass-panel">
                        <table id="tanksTable">
                            <thead>
                                <tr>
                                    <th>Tank ID</th>
                                    <th>Tank Name</th>
                                    <th>Capacity (Liters)</th>
                                    <th>Building Connected</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT wt.TANK_ID, wt.TANK_NAME, wt.CAPACITY_LITERS, b.BUILDING_NAME FROM water_tanks wt JOIN buildings b ON wt.BUILDING_ID = b.BUILDING_ID";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)){
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['TANK_ID']); ?></td>
                                    <td><?php echo htmlspecialchars($row['TANK_NAME']); ?></td>
                                    <td><?php echo number_format($row['CAPACITY_LITERS']); ?> L</td>
                                    <td><?php echo htmlspecialchars($row['BUILDING_NAME']); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                <section id="motors-view" class="view-section">
                    <div class="section-toolbar">
                        <div class="search-box">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="motorSearch" placeholder="Search motors...">
                        </div>
                        <button class="btn-primary" onclick="document.getElementById('addMotorFormContainer').style.display = 'block';">
                            <i class="ph ph-plus"></i> Add Motor
                        </button>
                    </div>
                    <div class="form-card glass-panel mb-4" id="addMotorFormContainer" style="display: none; margin-top: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3><i class="ph ph-plus-circle text-primary"></i> Register New Motor</h3>
                            <button onclick="document.getElementById('addMotorFormContainer').style.display = 'none';" style="background:none; border:none; cursor:pointer; font-size:1.2rem; color: #e74c3c;">✖</button>
                        </div>
                        <form class="inline-form" id="addMotorForm">
                            <input type="hidden" name="form_type" value="motor">
                            <div class="form-group">
                                <label>Motor ID</label>
                                <input type="text" name="motor_id" id="motor_id" placeholder="e.g. 201 or M-01" required>
                            </div>
                            <div class="form-group">
                                <label>Motor Name</label>
                                <input type="text" name="motor_name" id="motor_name" placeholder="e.g. Main Pump 1" required>
                            </div>
                            <div class="form-group">
                                <label>Power Rating</label>
                                <input type="text" name="power_rating" id="power_rating" placeholder="e.g. 5 HP" required>
                            </div>
                            <div class="form-group">
                                <label>Connected Tank</label>
                                <select name="tank_id" id="tank_id" required>
                                    <option value="">Select Tank...</option>
                                    <?php
                                    $t_res = $conn->query("SELECT TANK_ID, TANK_NAME FROM water_tanks");
                                    while($t_row = $t_res->fetch_assoc()) {
                                        echo "<option value='".$t_row['TANK_ID']."'>".$t_row['TANK_NAME']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary mt-auto">Save Motor</button>
                            </div>
                        </form>
                        <div id="motorFeedback" style="margin-top: 10px; font-weight: 500;"></div>
                    </div>
                    <div class="card-grid" id="motorCardGrid">
                        <?php
                        $sql = "SELECT * FROM motor_pumps";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <div class="data-card motor-card">
                            <div class="card-icon">
                                <i class="ph-fill ph-engine"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($row['MOTOR_NAME']); ?></h3>
                            <div class="card-details">
                                <p><i class="ph ph-lightning"></i> Power: <?php echo htmlspecialchars($row['POWER_RATING']); ?></p>
                                <p><i class="ph ph-cylinder"></i> Tank ID: <?php echo htmlspecialchars($row['TANK_ID']); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
                <section id="usage-view" class="view-section">
                    <div class="usage-dashboard">
                        <div class="form-card glass-panel mb-4">
                            <h3><i class="ph ph-plus-circle text-primary"></i> Add Daily Usage Record</h3>
                            <form class="inline-form" id="usageForm">
                                <input type="hidden" name="form_type" value="usage">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="usage_date" id="usage_date" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Building</label>
                                    <select name="building_id" id="building_id" required>
                                        <option value="">Select Building...</option>
                                        <?php
                                        $b_res = $conn->query("SELECT BUILDING_ID, BUILDING_NAME FROM buildings");
                                        while($b_row = $b_res->fetch_assoc()) {
                                            echo "<option value='".$b_row['BUILDING_ID']."'>".$b_row['BUILDING_NAME']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Consumption (Liters)</label>
                                    <input type="number" name="water_used" id="consumption" placeholder="e.g. 4500" required>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary mt-auto">Save Record</button>
                                </div>
                            </form>
                            <div id="formFeedback" style="margin-top: 10px; font-weight: 500;"></div>
                        </div>
                        <div class="table-container glass-panel">
                            <h3>Recent Consumption History</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Building</th>
                                        <th>Consumption</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT u.*, b.BUILDING_NAME FROM water_usage u JOIN buildings b ON u.BUILDING_ID = b.BUILDING_ID ORDER BY u.USAGE_DATE DESC";
                                    $result = mysqli_query($conn, $sql);
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $u_id = $row['USAGE_ID'];
                                            $amt = $row['WATER_USED_LITERS'];
                                            $date = $row['USAGE_DATE'];
                                            $building = $row['BUILDING_NAME'];
                                            echo "<tr>
                                                    <td>" . htmlspecialchars($date) . "</td>
                                                    <td>" . htmlspecialchars($building) . "</td>
                                                    <td><strong>" . number_format($amt) . " L</strong></td>
                                                    <td>
                                                        <button onclick='editUsage($u_id, $amt, \"$date\")' class='btn-icon text-primary' title='Edit'>
                                                            <i class='ph ph-pencil-simple'></i>
                                                        </button>
                                                        <button onclick='deleteRecord(\"water_usage\", $u_id)' class='btn-icon text-danger' title='Delete'>
                                                            <i class='ph ph-trash'></i>
                                                        </button>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                                <td colspan='4' style='text-align:center;'>No usage records found</td>
                                              </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section id="maintenance-view" class="view-section">
                    <?php
                    $pending_sql = "SELECT MOTOR_ID, ISSUE_DESCRIPTION FROM maintenance WHERE STATUS = 'Pending'";
                    $pending_result = mysqli_query($conn, $pending_sql);
                    if ($pending_result && mysqli_num_rows($pending_result) > 0) {
                    ?>
                    <div class="info-banner glass-panel warning-banner mb-4">
                        <i class="ph ph-warning-circle"></i>
                        <div>
                            <p><strong>Attention Required:</strong> You have <?php echo mysqli_num_rows($pending_result); ?> pending issue(s).</p>
                            <ul style="margin: 5px 0 0 20px; padding: 0; font-size: 0.9em;">
                                <?php 
                                while($issue = mysqli_fetch_assoc($pending_result)) {
                                    echo "<li><strong>Motor ID " . htmlspecialchars($issue['MOTOR_ID']) . ":</strong> " . htmlspecialchars($issue['ISSUE_DESCRIPTION']) . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="info-banner glass-panel mb-4" style="border-left: 4px solid #2ecc71;">
                        <i class="ph ph-check-circle" style="color: #2ecc71;"></i>
                        <p><strong>All clear:</strong> There are currently no pending maintenance issues.</p>
                    </div>
                    <?php } ?>
                    <div class="form-card glass-panel mb-4">
                        <h3><i class="ph ph-plus-circle text-primary"></i> Add Maintenance Record</h3>
                        <form class="inline-form" id="maintenanceForm">
                            <input type="hidden" name="form_type" value="maintenance">
                            <div class="form-group">
                                <label>Maintenance Date</label>
                                <input type="date" name="maintenance_date" id="maintenance_date" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Issue Description</label>
                                <input type="text" name="issue_description" id="issue_description" placeholder="Describe the issue..." required>
                            </div>
                             <div class="form-group">
                                <label>Motor ID</label>
                                <input type="text" name="motor_id" id="motor_id" placeholder="Enter motor ID..." required>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" required>
                                    <option value="">Select Status...</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary mt-auto">Save Record</button>
                            </div>
                        </form>
                        <div id="maintenanceFeedback" style="margin-top: 10px; font-weight: 500;"></div>
                    </div>
                    <div class="table-container glass-panel">
                        <table>
                            <thead>
                                <tr>
                                    <th>Motor ID</th>
                                    <th>Building</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Issue Description</th>
                                    <th>Actions</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $maintSql = "SELECT m.MAINTENANCE_ID, m.STATUS, m.MOTOR_ID, m.ISSUE_DESCRIPTION, b.LOCATION, b.BUILDING_NAME FROM MAINTENANCE m JOIN MOTOR_PUMPS mp ON m.MOTOR_ID = mp.MOTOR_ID JOIN WATER_TANKS wt ON mp.TANK_ID = wt.TANK_ID JOIN BUILDINGS b ON wt.BUILDING_ID = b.BUILDING_ID ORDER BY m.MAINTENANCE_DATE DESC";
                                $maintResult = mysqli_query($conn, $maintSql);
                                if($maintResult && mysqli_num_rows($maintResult) > 0) {
                                    while($row = mysqli_fetch_assoc($maintResult)) {
                                        $m_id = $row['MAINTENANCE_ID'];
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)$row['MOTOR_ID']); ?></td>
                                    <td><?php echo htmlspecialchars($row['BUILDING_NAME']); ?></td>
                                    <td><?php echo htmlspecialchars($row['LOCATION']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['STATUS']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['ISSUE_DESCRIPTION']); ?></td>
                                    <td>
                                        <div class="card-actions">
                                            <button onclick="deleteRecord('maintenance', <?php echo $m_id; ?>)" class="btn-icon text-danger" title="Delete Log">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center;'>No maintenance records found</td></tr>";
                                }
                                ?>
                            </tbody> 
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>