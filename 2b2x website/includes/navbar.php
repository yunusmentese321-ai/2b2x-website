<?php
$current_user = current_user();
$active = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
?>
<header class="site-header" data-header>
  <nav class="navbar container" aria-label="Primary navigation">
    <a class="brand" href="<?= e(app_url('index.php')) ?>" aria-label="2B2X Browser home"><span class="brand-mark"><i></i><i></i></span><span>2B2X</span><small>Browser</small></a>
    <div class="nav-links" data-nav-links>
      <a href="<?= e(app_url('index.php#features')) ?>" class="<?= $active === 'index.php' ? 'active' : '' ?>">Features</a>
      <a href="<?= e(app_url('download.php')) ?>">Download</a>
      <a href="<?= e(app_url('changelog.php')) ?>">Changelog</a>
      <a href="<?= e(app_url('blog.php')) ?>">Blog</a>
      <a href="<?= e(app_url('support.php')) ?>">Support</a>
    </div>
    <div class="nav-actions">
      <?php if ($current_user): ?><a class="nav-account" href="<?= e(app_url('account.php')) ?>"><span><?= e($current_user['username']) ?></span><span class="status-dot"></span></a><a class="button button-ghost button-small" href="<?= e(app_url('logout.php')) ?>">Logout</a><?php else: ?><a class="button button-ghost button-small" href="<?= e(app_url('login.php')) ?>">Login</a><?php endif; ?>
      <a class="button button-primary button-small nav-download" href="<?= e(app_url('download.php')) ?>">Download <span aria-hidden="true">↗</span></a>
      <button class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" aria-label="Open navigation"><span></span><span></span></button>
    </div>
  </nav>
  <div class="mobile-menu" id="mobile-menu" data-mobile-menu aria-hidden="true">
    <div class="mobile-menu-inner">
      <a href="<?= e(app_url('index.php#features')) ?>">Features</a><a href="<?= e(app_url('download.php')) ?>">Download</a><a href="<?= e(app_url('changelog.php')) ?>">Changelog</a><a href="<?= e(app_url('blog.php')) ?>">Blog</a><a href="<?= e(app_url('support.php')) ?>">Support</a>
      <?php if ($current_user): ?><a href="<?= e(app_url('account.php')) ?>">Account</a><a href="<?= e(app_url('logout.php')) ?>">Logout</a><?php else: ?><a href="<?= e(app_url('login.php')) ?>">Login</a><?php endif; ?>
    </div>
  </div>
</header>
