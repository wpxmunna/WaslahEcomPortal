<div class="account-sidebar">
    <div class="text-center mb-4">
        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
             style="width: 80px; height: 80px; font-size: 32px;">
            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
        </div>
        <h5 class="mt-3 mb-0"><?= sanitize($user['name'] ?? 'User') ?></h5>
        <small class="text-muted"><?= $user['email'] ?? '' ?></small>
    </div>

    <ul class="account-menu">
        <li>
            <a href="<?= url('account') ?>" class="<?= activeClass('account') ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= url('account/orders') ?>" class="<?= activeClass('account/orders') ?>">
                <i class="fas fa-shopping-bag"></i> My Orders
            </a>
        </li>
        <li>
            <a href="<?= url('wishlist') ?>" class="<?= activeClass('wishlist') ?>">
                <i class="fas fa-heart"></i> Wishlist
            </a>
        </li>
        <li>
            <a href="<?= url('account/addresses') ?>" class="<?= activeClass('account/addresses') ?>">
                <i class="fas fa-map-marker-alt"></i> Addresses
            </a>
        </li>
        <li>
            <a href="<?= url('account/profile') ?>" class="<?= activeClass('account/profile') ?>">
                <i class="fas fa-user-edit"></i> Profile Settings
            </a>
        </li>
        <li>
            <a href="<?= url('logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>
