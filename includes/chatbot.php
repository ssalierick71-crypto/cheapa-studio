<?php if (!defined('SITE_NAME')) require_once dirname(__DIR__) . '/config.php'; ?>
<!-- ════════════════════════ CHATBOT (24/7 sales assistant) ════════════════════════ -->
<button class="chat-fab" id="chatFab" aria-label="Open chat">
  <i class="bi bi-chat-dots-fill"></i>
  <span class="chat-fab-dot"></span>
</button>

<div class="chat-widget" id="chatWidget" aria-hidden="true">
  <div class="chat-head">
    <div class="chat-head-id">
      <span class="chat-avatar">CS</span>
      <div>
        <strong><?= SITE_NAME ?></strong>
        <span class="chat-status"><span class="dot"></span> Online — replies in minutes</span>
      </div>
    </div>
    <button class="icon-btn" id="chatCloseBtn" aria-label="Close chat"><i class="bi bi-x-lg"></i></button>
  </div>

  <div class="chat-body" id="chatBody"><!-- JS-rendered conversation --></div>

  <div class="chat-quick" id="chatQuick"><!-- JS-rendered quick-reply buttons --></div>

  <div class="chat-foot">
    <a href="<?= wa_link('Hello Cheapa Studio! I have a question.') ?>" target="_blank" rel="noopener" class="chat-wa-link">
      <i class="bi bi-whatsapp"></i> Continue on WhatsApp
    </a>
  </div>
</div>

<script>
  // WhatsApp number exposed for client-side checkout / chat hand-off.
  window.CHEAPA = {
    wa: '<?= e(preg_replace('/\D/', '', cfg('whatsapp_number'))) ?>',
    currency: '<?= CURRENCY ?>',
    siteUrl: '<?= SITE_URL ?>'
  };
</script>
