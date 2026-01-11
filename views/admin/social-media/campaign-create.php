<?php
/**
 * Campaign Messages - Create View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-plus-circle"></i> Create Campaign Message</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media') ?>">Social Media</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media/campaigns') ?>">Campaigns</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
</div>

<form action="<?= url('admin/social-media/campaigns/store') ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Message Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               placeholder="e.g., Weekend Sale Announcement">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Platform</label>
                            <select name="platform" id="platform" class="form-select">
                                <?php foreach ($platforms as $key => $platform): ?>
                                <option value="<?= $key ?>" data-icon="<?= $platform['icon'] ?>" data-color="<?= $platform['color'] ?>">
                                    <?= $platform['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Message Type</label>
                            <select name="message_type" id="message_type" class="form-select">
                                <?php foreach ($messageTypes as $key => $type): ?>
                                <option value="<?= $key ?>"><?= $type['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Message <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="8" required
                                  placeholder="Write your complete campaign message here..."></textarea>
                        <small class="text-muted">
                            <span id="charCount">0</span> characters
                            | Instagram: 2200 max | Facebook: 63,206 max | WhatsApp: 65,536 max
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Version <small class="text-muted">(for previews/stories)</small></label>
                        <textarea name="short_content" class="form-control" rows="2" maxlength="500"
                                  placeholder="A shorter version for stories or previews..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hashtags</label>
                        <input type="text" name="hashtags" class="form-control"
                               placeholder="#Sale #Fashion #WaslahStyle #ShopNow">
                        <small class="text-muted">Separate with spaces, include # symbol</small>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Call to Action</label>
                            <input type="text" name="call_to_action" class="form-control"
                                   placeholder="e.g., Shop Now, Learn More, Swipe Up">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CTA Link</label>
                            <input type="url" name="cta_url" class="form-control"
                                   placeholder="https://waslah.com/sale">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Campaign Image</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: 1080x1080px for Instagram, 1200x630px for Facebook</small>
                    </div>
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Settings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned">
                            <label class="form-check-label" for="is_pinned">Pin to Top</label>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Schedule For</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control">
                        <small class="text-muted">Leave empty to publish immediately</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expires At</label>
                        <input type="datetime-local" name="expires_at" class="form-control">
                        <small class="text-muted">Leave empty for no expiration</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Create Campaign
                    </button>
                    <a href="<?= url('admin/social-media/campaigns') ?>" class="btn btn-outline-secondary w-100 mt-2">
                        Cancel
                    </a>
                </div>
            </div>

            <!-- Preview -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
                </div>
                <div class="card-body">
                    <div id="preview" class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <div id="platformIcon" class="me-2" style="width: 32px; height: 32px; border-radius: 50%; background: #34495e; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-globe"></i>
                            </div>
                            <strong>Waslah Fashion</strong>
                        </div>
                        <div id="previewContent" class="text-muted small" style="white-space: pre-wrap;">
                            Your message preview will appear here...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Templates -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Quick Templates</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2" onclick="loadTemplate('sale')">
                        <i class="fas fa-tag me-2"></i>Sale Announcement
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2" onclick="loadTemplate('new')">
                        <i class="fas fa-star me-2"></i>New Arrival
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2" onclick="loadTemplate('greeting')">
                        <i class="fas fa-heart me-2"></i>Festival Greeting
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="loadTemplate('thanks')">
                        <i class="fas fa-hands-clapping me-2"></i>Thank You
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Character counter
document.getElementById('content').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
    document.getElementById('previewContent').textContent = this.value || 'Your message preview will appear here...';
});

// Platform icon update
document.getElementById('platform').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const icon = option.dataset.icon || 'fa-globe';
    const color = option.dataset.color || '#34495e';
    const iconEl = document.getElementById('platformIcon');
    iconEl.style.backgroundColor = color;
    iconEl.innerHTML = `<i class="fa-brands ${icon}"></i>`;
});

// Image preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.style.display = 'block';
            preview.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Quick templates
const templates = {
    sale: {
        title: 'Flash Sale Announcement',
        content: `FLASH SALE ALERT!\n\nGet up to 50% OFF on selected items this weekend only!\n\nUse code: FLASH50 at checkout\n\nDon't miss out - limited stock available!\n\nShop now at waslah.com`,
        hashtags: '#Sale #FlashSale #Discount #Fashion #WaslahFashion #ShopNow',
        cta: 'Shop Now'
    },
    new: {
        title: 'New Collection Launch',
        content: `Introducing our NEW collection!\n\nFresh styles, premium quality, and affordable prices.\n\nBe the first to shop our latest arrivals.\n\nLink in bio!`,
        hashtags: '#NewArrivals #NewCollection #Fashion #Style #Waslah #OOTD',
        cta: 'Explore Now'
    },
    greeting: {
        title: 'Festival Greeting',
        content: `Wishing you and your loved ones a blessed celebration!\n\nMay this special occasion bring joy, peace, and prosperity.\n\nCelebrate in style with our festive collection - Now 30% OFF!`,
        hashtags: '#Celebration #FestivalVibes #WaslahFamily',
        cta: 'Shop Festive'
    },
    thanks: {
        title: 'Thank You Message',
        content: `THANK YOU for being part of the Waslah family!\n\nYour support means everything to us.\n\nAs a token of appreciation, enjoy 15% off your next order.\n\nUse code: THANKYOU15`,
        hashtags: '#ThankYou #CustomerLove #WaslahFamily',
        cta: 'Claim Discount'
    }
};

function loadTemplate(type) {
    const template = templates[type];
    if (template) {
        document.querySelector('input[name="title"]').value = template.title;
        document.getElementById('content').value = template.content;
        document.querySelector('input[name="hashtags"]').value = template.hashtags;
        document.querySelector('input[name="call_to_action"]').value = template.cta;

        // Trigger updates
        document.getElementById('content').dispatchEvent(new Event('input'));
    }
}
</script>
