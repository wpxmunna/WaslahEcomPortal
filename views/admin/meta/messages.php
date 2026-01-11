<?php
/**
 * Meta Messages Inbox
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-inbox"></i> Messages Inbox</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/meta') ?>">Meta Integration</a></li>
                <li class="breadcrumb-item active">Messages</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Platform Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="<?= url('admin/meta/messages') ?>"
               class="btn <?= !$currentPlatform ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-inbox me-2"></i>All
            </a>
            <?php
            $fb = $ig = $wa = null;
            foreach ($integrations as $i) {
                if ($i['platform'] === 'facebook' && $i['is_active']) $fb = $i;
                if ($i['platform'] === 'instagram' && $i['is_active']) $ig = $i;
                if ($i['platform'] === 'whatsapp' && $i['is_active']) $wa = $i;
            }
            ?>
            <?php if ($fb): ?>
            <a href="<?= url('admin/meta/messages/facebook') ?>"
               class="btn <?= $currentPlatform === 'facebook' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fab fa-facebook-messenger me-2"></i>Messenger
            </a>
            <?php endif; ?>
            <?php if ($ig): ?>
            <a href="<?= url('admin/meta/messages/instagram') ?>"
               class="btn <?= $currentPlatform === 'instagram' ? 'btn-danger' : 'btn-outline-danger' ?>">
                <i class="fab fa-instagram me-2"></i>Instagram
            </a>
            <?php endif; ?>
            <?php if ($wa): ?>
            <a href="<?= url('admin/meta/messages/whatsapp') ?>"
               class="btn <?= $currentPlatform === 'whatsapp' ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="fab fa-whatsapp me-2"></i>WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <!-- Conversations List -->
    <div class="col-md-4">
        <div class="card" style="height: calc(100vh - 280px); overflow: hidden;">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-comments me-2"></i>Conversations
                    <span class="badge bg-primary ms-2"><?= count($conversations) ?></span>
                </h6>
            </div>
            <div class="list-group list-group-flush" style="overflow-y: auto; height: 100%;" id="conversationsList">
                <?php if (empty($conversations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No conversations yet</p>
                </div>
                <?php else: ?>
                <?php foreach ($conversations as $idx => $conv): ?>
                <a href="#" class="list-group-item list-group-item-action conversation-item <?= $idx === 0 ? 'active' : '' ?>"
                   data-sender-id="<?= htmlspecialchars($conv['sender_id']) ?>"
                   data-platform="<?= htmlspecialchars($conv['platform']) ?>"
                   onclick="selectConversation(this, <?= htmlspecialchars(json_encode($conv)) ?>); return false;">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <?php if ($conv['platform'] === 'facebook'): ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="fab fa-facebook-messenger"></i>
                            </div>
                            <?php elseif ($conv['platform'] === 'instagram'): ?>
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <?php else: ?>
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($conv['sender_name'] ?? 'Unknown') ?></h6>
                                <?php if ($conv['last_message']): ?>
                                <small class="text-truncate d-block" style="max-width: 150px;">
                                    <?= htmlspecialchars(substr($conv['last_message']['content'], 0, 50)) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php if ($conv['unread'] > 0): ?>
                            <span class="badge bg-primary rounded-pill"><?= $conv['unread'] ?></span>
                            <?php endif; ?>
                            <?php if ($conv['last_message']): ?>
                            <small class="text-muted d-block">
                                <?= date('M j', strtotime($conv['last_message']['created_at'])) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Message Thread -->
    <div class="col-md-8">
        <div class="card" style="height: calc(100vh - 280px); display: flex; flex-direction: column;">
            <div class="card-header" id="chatHeader">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;" id="chatAvatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h6 class="mb-0" id="chatName">Select a conversation</h6>
                        <small class="text-muted" id="chatPlatform"></small>
                    </div>
                </div>
            </div>
            <div class="card-body" style="flex: 1; overflow-y: auto; background: #f8f9fa;" id="messageThread">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-comments fa-4x mb-3"></i>
                    <p>Select a conversation to view messages</p>
                </div>
            </div>
            <div class="card-footer" id="replyForm" style="display: none;">
                <form onsubmit="sendReply(event)">
                    <input type="hidden" id="replyPlatform" value="">
                    <input type="hidden" id="replyRecipient" value="">
                    <div class="input-group">
                        <input type="text" class="form-control" id="replyMessage" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentConversation = null;

function selectConversation(element, conversation) {
    // Update active state
    document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    currentConversation = conversation;

    // Update header
    const platformIcons = {
        facebook: '<i class="fab fa-facebook-messenger"></i>',
        instagram: '<i class="fab fa-instagram"></i>',
        whatsapp: '<i class="fab fa-whatsapp"></i>'
    };
    const platformColors = {
        facebook: 'bg-primary',
        instagram: 'bg-danger',
        whatsapp: 'bg-success'
    };

    document.getElementById('chatAvatar').className = `rounded-circle ${platformColors[conversation.platform]} text-white d-flex align-items-center justify-content-center me-2`;
    document.getElementById('chatAvatar').style.cssText = 'width: 40px; height: 40px;';
    document.getElementById('chatAvatar').innerHTML = platformIcons[conversation.platform];
    document.getElementById('chatName').textContent = conversation.sender_name || 'Unknown';
    document.getElementById('chatPlatform').textContent = conversation.platform.charAt(0).toUpperCase() + conversation.platform.slice(1);

    // Show messages
    const thread = document.getElementById('messageThread');
    thread.innerHTML = '';

    if (conversation.messages && conversation.messages.length > 0) {
        // Sort messages oldest first
        const messages = [...conversation.messages].reverse();

        messages.forEach(msg => {
            const isIncoming = msg.is_incoming == 1;
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const date = new Date(msg.created_at).toLocaleDateString();

            const msgHtml = `
                <div class="d-flex ${isIncoming ? '' : 'justify-content-end'} mb-3">
                    <div class="p-3 rounded-3 ${isIncoming ? 'bg-white' : 'bg-primary text-white'}" style="max-width: 70%;">
                        <p class="mb-1">${escapeHtml(msg.content)}</p>
                        <small class="${isIncoming ? 'text-muted' : 'text-white-50'}">${time} - ${date}</small>
                    </div>
                </div>
            `;
            thread.innerHTML += msgHtml;
        });

        thread.scrollTop = thread.scrollHeight;
    } else {
        thread.innerHTML = '<div class="text-center py-5 text-muted"><p>No messages in this conversation</p></div>';
    }

    // Show reply form
    document.getElementById('replyForm').style.display = 'block';
    document.getElementById('replyPlatform').value = conversation.platform;
    document.getElementById('replyRecipient').value = conversation.sender_id;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function sendReply(event) {
    event.preventDefault();

    const platform = document.getElementById('replyPlatform').value;
    const recipient = document.getElementById('replyRecipient').value;
    const message = document.getElementById('replyMessage').value.trim();

    if (!message) return;

    const btn = document.getElementById('sendBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('<?= url('admin/meta/send-message') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            csrf_token: '<?= Session::getCsrfToken() ?>',
            platform: platform,
            recipient_id: recipient,
            message: message
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Add message to thread
            const thread = document.getElementById('messageThread');
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const msgHtml = `
                <div class="d-flex justify-content-end mb-3">
                    <div class="p-3 rounded-3 bg-primary text-white" style="max-width: 70%;">
                        <p class="mb-1">${escapeHtml(message)}</p>
                        <small class="text-white-50">${time} - Just now</small>
                    </div>
                </div>
            `;
            thread.innerHTML += msgHtml;
            thread.scrollTop = thread.scrollHeight;

            document.getElementById('replyMessage').value = '';
        } else {
            alert(data.message || 'Failed to send message');
        }
    })
    .catch(err => {
        alert('Error sending message');
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    });
}

// Auto-select first conversation
document.addEventListener('DOMContentLoaded', function() {
    const firstConv = document.querySelector('.conversation-item');
    if (firstConv) {
        firstConv.click();
    }
});
</script>
