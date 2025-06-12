<?php
require_once '../php/User.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once '../php/UserController.php';
// Check if user is logged in
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
  header("Location: ../HTML/login.html");
  exit();
}



$controller = new UserController();

$totalEventos = $controller->returntotalevents();
$totalJuegos = $controller->getUserInterestedGamesCount();
$controller->getPromotorEvents();

if(empty($misEventos)){
  $misEventos[0]['total_eventos'] = 0;
}


$juegosInteresados = $controller->getUserInterestedGames();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NextPlay - Promotor Profile</title>

  <!-- Website Icon -->
  <link rel="icon" href="../imagenes/logo.png">

  <!-- Custom Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Press+Start+2P&display=swap"
    rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- jQuery and Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../Layout/include.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom Styles -->
  <link rel="stylesheet" href="../Layout/layout.css">
  <link rel="stylesheet" href="../CSS/perfil.css">
</head>

<body>
  <!-- Navigation Bar -->
  <header>
    <div id="navbar"></div>
  </header>

  <main>
    <div class="profile-container">
      <div class="container">
        <!-- Display error messages if any -->
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="row">
          <!-- Left Sidebar - Player Information -->
          <div class="col-lg-4 col-md-5">
            <div class="profile-card">
              <div class="profile-header">
                <div class="level-badge">Promotor</div>
                <h2 class="profile-title"> Profile</h2>
              </div>

              <div class="profile-avatar-container">
                <?php
                $username = $_SESSION['user']->getNombre();
                $imagePath = "../users/profileimg/" . $username . ".jpg";
                if (file_exists($imagePath)) {
                  $avatarSrc = $imagePath;
                } else {
                  $avatarSrc = "http://ssl.gstatic.com/accounts/ui/avatar_2x.png";
                }
                ?>
                <!-- Avatar -->
                <div class="avatar-frame">
                  <img src="<?= $avatarSrc ?>" id="profile-image" class="profile-avatar" alt="Player Avatar">
                  <div class="avatar-overlay">
                    <i class="fas fa-camera"></i>
                    <span>Change Avatar</span>
                  </div>
                </div>

                <!-- Username -->
                <h3 class="profile-username"><?php echo $_SESSION['user']->getNombre(); ?></h3>

                <!-- Avatar Upload Form -->
                <form id="upload-form" enctype="multipart/form-data">
                  <input type="file" id="file-upload" name="profile_image" class="d-none" accept="image/*"
                    onchange="previewAndSubmit(event)">
                </form>
              </div>

              <div class="profile-stats">
                <div class="stat-item">
                  <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                  <div class="stat-info">
                    <span class="stat-label">Events Created</span>
                    <span class="stat-value"><?php echo $misEventos[0]['total_eventos']; ?></span>
                  </div>
                </div>
              </div>

              <!-- <div class="progress-container">
                <div class="progress-label">
                  <span>Experience</span>
                  <span>750/1000</span>
                </div>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0"
                    aria-valuemax="100"></div>
                </div>
              </div>-->
            </div>

          </div>

          <!-- Right Content - Profile Form -->
          <div class="col-lg-8 col-md-7">
            <div class="profile-card">
              <ul class="nav nav-tabs profile-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                    data-bs-target="#profile-content" type="button" role="tab" aria-controls="profile-content"
                    aria-selected="true">
                    <i class="fas fa-user"></i> Profile
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-content"
                    type="button" role="tab" aria-controls="security-content" aria-selected="false">
                    <i class="fas fa-lock"></i> Security
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="achievements-tab" data-bs-toggle="tab"
                    data-bs-target="#achievements-content" type="button" role="tab" aria-controls="achievements-content"
                    aria-selected="false">
                    <i class="fas fa-award"></i> Achievements
                  </button>
                </li>
              </ul>

              <div class="tab-content p-4" id="profileTabsContent">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile-content" role="tabpanel"
                  aria-labelledby="profile-tab">
                  <!-- Using controllerpdo.php's update method -->
                  <form class="profile-form" action="../php/UserController.php" method="POST">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <label for="nombre" class="form-label">
                            <i class="fas fa-user"></i> Username
                          </label>
                          <input type="text" class="form-control" name="username" id="nombre"
                            value="<?php echo $_SESSION['user']->getNombre(); ?>">
                        </div>
                      </div>

                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <label for="numero" class="form-label">
                            <i class="fas fa-envelope"></i> Numero
                          </label>
                          <input type="numero" class="form-control" name="numero" id="numero"
                            value="<?php echo $_SESSION['user']->getNumero(); ?>">
                        </div>
                      </div>

                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                          </label>
                          <input type="email" class="form-control" name="email" id="email"
                            value="<?php echo $_SESSION['user']->getCorreo(); ?>">
                        </div>
                      </div>
                    </div>

                    <!--<div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <label for="teléfono" class="form-label">
                            <i class="fas fa-phone"></i> Phone
                          </label>
                          <input type="text" class="form-control" name="teléfono" id="teléfono"
                            placeholder="Enter phone number">
                        </div>
                      </div>

                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <label for="birthdate" class="form-label">
                            <i class="fas fa-birthday-cake"></i> Birthday
                          </label>
                          <input type="date" class="form-control" name="birthdate" id="birthdate">
                        </div>
                      </div>
                    </div>

                    <div class="form-group mb-3">
                      <label for="bio" class="form-label">
                        <i class="fas fa-comment-alt"></i> Bio
                      </label>
                      <textarea class="form-control" name="bio" id="bio" rows="3"
                        placeholder="Introduce yourself..."></textarea>
                    </div>-->

                    <!--<div class="form-group mb-3">
                        <label class="form-label">
                        <i class="fas fa-gamepad"></i> Game Preferences
                      </label>
                      <div class="game-preferences">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="pref-action" value="action">
                          <label class="form-check-label" for="pref-action">Action</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="pref-rpg" value="rpg">
                          <label class="form-check-label" for="pref-rpg">RPG</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="pref-strategy" value="strategy">
                          <label class="form-check-label" for="pref-strategy">Strategy</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="pref-sports" value="sports">
                          <label class="form-check-label" for="pref-sports">Sports</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="pref-puzzle" value="puzzle">
                          <label class="form-check-label" for="pref-puzzle">Puzzle</label>
                        </div>
                      </div>
                    </div>-->

                    <div class="form-buttons">
                      <button type="submit" name="update" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                      </button>
                      <button type="reset" class="btn-reset">
                        <i class="fas fa-undo"></i> Reset
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security-content" role="tabpanel" aria-labelledby="security-tab">
                  <!-- Using controllerpdo.php's updatePassword method -->
                  <form class="profile-form" action="../php/UserController.php" method="POST">
                    <div class="form-group mb-3">
                      <label for="current_password" class="form-label">
                        <i class="fas fa-key"></i> Current Password
                      </label>
                      <input type="password" class="form-control" name="current_password" id="current_password"
                        placeholder="Enter current password">
                    </div>

                    <div class="form-group mb-3">
                      <label for="new_password" class="form-label">
                        <i class="fas fa-lock"></i> New Password
                      </label>
                      <input type="password" class="form-control" name="new_password" id="new_password"
                        placeholder="Enter new password">
                    </div>

                    <div class="form-group mb-3">
                      <label for="repeat_new_password" class="form-label">
                        <i class="fas fa-check-circle"></i> Confirm New Password
                      </label>
                      <input type="password" class="form-control" name="repeat_new_password" id="repeat_new_password"
                        placeholder="Re-enter new password">
                    </div>

                    <div class="form-buttons">
                      <button type="submit" name="update_password" class="btn-save">
                        <i class="fas fa-key"></i> Update Password
                      </button>
                    </div>

                    <hr class="my-4">

                    <div class="account-actions">
                      <h4 class="section-title">Account Actions</h4>
                      <div class="action-buttons">
                        <!-- Using controllerpdo.php's logout method -->
                        <button type="submit" name="logout" class="btn-logout">
                          <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                        <button type="button" class="btn-danger" data-bs-toggle="modal"
                          data-bs-target="#deleteAccountModal">
                          <i class="fas fa-trash-alt"></i> Delete Account
                        </button>
                      </div>
                    </div>
                  </form>
                </div>

                <!-- Achievements Tab -->
                <div class="tab-pane fade" id="achievements-content" role="tabpanel" aria-labelledby="achievements-tab">
                  <div class="achievements-container">
                    <div class="row">
                      <div class="col-md-6 col-lg-4 mb-4">
                        <div class="achievement-card unlocked">
                          <div class="achievement-icon">
                            <i class="fas fa-user-check"></i>
                          </div>
                          <div class="achievement-info">
                            <h4>First Login</h4>
                            <p>First time logging into the game platform</p>
                          </div>
                          <div class="achievement-date">Earned on: 2023-05-15</div>
                        </div>
                      </div>

                      <div class="col-md-6 col-lg-4 mb-4">
                        <div class="achievement-card unlocked">
                          <div class="achievement-icon">
                            <i class="fas fa-image"></i>
                          </div>
                          <div class="achievement-info">
                            <h4>Personalization</h4>
                            <p>Upload a custom avatar</p>
                          </div>
                          <div class="achievement-date">Earned on: 2023-05-16</div>
                        </div>
                      </div>

                      <div class="col-md-6 col-lg-4 mb-4">
                        <div class="achievement-card locked">
                          <div class="achievement-icon">
                            <i class="fas fa-calendar-week"></i>
                          </div>
                          <div class="achievement-info">
                            <h4>Event Master</h4>
                            <p>Participate in 10 game events</p>
                          </div>
                          <div class="achievement-progress">Progress: 5/10</div>
                        </div>
                      </div>

                      <div class="col-md-6 col-lg-4 mb-4">
                        <div class="achievement-card locked">
                          <div class="achievement-icon">
                            <i class="fas fa-star"></i>
                          </div>
                          <div class="achievement-info">
                            <h4>Community Star</h4>
                            <p>Receive 50 likes</p>
                          </div>
                          <div class="achievement-progress">Progress: 12/50</div>
                        </div>
                      </div>

                      <div class="col-md-6 col-lg-4 mb-4">
                        <div class="achievement-card locked">
                          <div class="achievement-icon">
                            <i class="fas fa-medal"></i>
                          </div>
                          <div class="achievement-info">
                            <h4>Game Master</h4>
                            <p>Play 20 different games on the platform</p>
                          </div>
                          <div class="achievement-progress">Progress: 3/20</div>
                        </div>
                      </div>

                      <div class="col-md-6 col-lg-4 mb-4">
                        <div class="achievement-card locked">
                          <div class="achievement-icon">
                            <i class="fas fa-clock"></i>
                          </div>
                          <div class="achievement-info">
                            <h4>Gaming Enthusiast</h4>
                            <p>Accumulate 500 hours of game time</p>
                          </div>
                          <div class="achievement-progress">Progress: 120/500</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Events List -->
            <div class="profile-card mt-4">

              <h3 class="card-title"><i class="fas fa-history"></i>Events created</h3>
              <a href="manager.php"><button class="btn-save">Manage events</button></a>
              <ul class="activity-list">




                <?php
                if($misEventos[0]['total_eventos'] == 0){
                  echo '<p>No events</p>';
                } else{

                

                foreach ($misEventos as $evento) {
                  echo '
    <li class="activity-item">
      <div class="activity-icon"><i class="fas fa-calendar-check"></i></div>
      <div class="activity-content">
        <div class="activity-title"><a href="event.php?id=' . htmlspecialchars($evento['id_evento']) . '">' . htmlspecialchars($evento['nombre']) . '</a></div>
        <div class="activity-time">' . htmlspecialchars($evento['fecha']) . '</div>
      </div>
    </li>';
                }}
                ?>
              </ul>
            </div>
            <!--   -->
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-danger">Warning: This action will permanently delete your account and all associated data.
              This cannot be undone.</p>
            <p>Please enter your password to confirm deletion:</p>
            <!-- Using controllerpdo.php's delete method -->
            <form id="delete-account-form" action="../php/UserController.php" method="POST">
              <div class="form-group">
                <input type="password" class="form-control" name="confirm_password" placeholder="Enter password"
                  required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="delete-account-form" name="delete" class="btn-danger">Confirm Deletion</button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <div id="footer"></div>

  <!-- Avatar Upload Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const profileImage = document.getElementById('profile-image');
      const avatarOverlay = document.querySelector('.avatar-overlay');
      const fileInput = document.getElementById('file-upload');
      const uploadForm = document.getElementById('upload-form');

      // Click avatar or overlay to activate file selector
      profileImage.addEventListener('click', () => {
        fileInput.click();
      });

      avatarOverlay.addEventListener('click', () => {
        fileInput.click();
      });

      // Preview and submit selected image
      window.previewAndSubmit = function (event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            profileImage.src = e.target.result; // Update avatar preview
          };
          reader.readAsDataURL(file);

          // Create FormData object to send file
          const formData = new FormData();
          formData.append("profile_image", file);

          // Send image via AJAX
          $.ajax({
            url: '/dam1/NextPlay/php/uploadpicture.php', // PHP script path
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              try {
                const data = JSON.parse(response);
                if (data.status === "success") {
                  showNotification('success', data.message);
                } else {
                  showNotification('error', data.message);
                }
              } catch (e) {
                showNotification('error', 'Error occurred during upload');
              }
            },
          });
        }
      };

      // Show notification
      function showNotification(type, message) {
        const notificationDiv = document.createElement('div');
        notificationDiv.className = `notification ${type}`;
        notificationDiv.innerHTML = `
          <div class="notification-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
          </div>
          <div class="notification-message">${message}</div>
        `;

        document.body.appendChild(notificationDiv);

        // Show notification
        setTimeout(() => {
          notificationDiv.classList.add('show');
        }, 100);

        // Hide notification after 3 seconds
        setTimeout(() => {
          notificationDiv.classList.remove('show');
          setTimeout(() => {
            document.body.removeChild(notificationDiv);
          }, 300);
        }, 3000);
      }
    });
  </script>
</body>

</html>