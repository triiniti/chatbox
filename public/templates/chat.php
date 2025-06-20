<?php $this->layout('template', ['title' => 'Chatbox']) ?>

<div class="container">
  <div class="chatbox-card" id="chatbox-card">
    <div class="top-bar">
      <div class="circles">
        <div id="close-circle" class="circle" title="Logout"></div>
        <div id="minimize-circle" class="circle" title="Minimize"></div>
        <div id="maximize-circle" class="circle" title="Maximize"></div>
      </div>
    </div>
    <div class="content">
      <div class="chatbox-panel" id="chatbox-panel">
        <div id="chatbox-messages">
          <?php foreach ($messages as $message): ?>
            <?php if ($message['position'] === 'right'): ?>
              <div class="chat-item iright">
                <div class="chat-bubble"><?= $message['content'] ?></div>
                <img src="<?= $message['avatar'] ?>" class="chat-avatar" alt="Avatar">
              </div>
            <?php else: ?>
              <div class="chat-item">
                <img src="<?= $message['avatar'] ?>" class="chat-avatar" alt="Avatar">
                <div class="chat-bubble"><?= $message['content'] ?></div>
              </div>
            <?php endif; ?>
            <div class="break"></div>
          <?php endforeach; ?>
        </div>
      </div>
      <form name="chatbox-form" action="/chat" method="POST" id="chatbox-form" autocomplete="off">
        <div class="chatbox-input-area">
          <?= $csrf_token ?>
          <textarea name="message" id="message" class="chatbox-input" placeholder="Type your message…"
            required></textarea>
          <button class="btn-send" type="submit" name="action" title="Send">&#x27A4;</button>
        </div>
        <div class="chatbox-footer">
          <a href="/logout" class="btn-logout" id="logout-btn">
            <span style="vertical-align: middle;">&#x21aa;</span> Logout
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Topbar buttons
    document.getElementById("close-circle").addEventListener("click", function () {
      document.getElementById("logout-btn").click();
    });

    document.getElementById("maximize-circle").addEventListener("click", function () {
      const card = document.getElementById("chatbox-card");
      const panel = document.getElementById("chatbox-panel");
      card.classList.toggle("chatbox-maximized");
      document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
      //panel.classList.toggle("panel-maximized");
      //document.body.classList.toggle("no-scroll", card.classList.contains("panel-maximized"));
    });

    document.getElementById("minimize-circle").addEventListener("click", function () {
      const card = document.getElementById("chatbox-card");
      const panel = document.getElementById("chatbox-panel");
      card.classList.toggle("chatbox-maximized");
      document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
      //panel.classList.toggle("chatbox-maximized");
      //document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
    });

    document.getElementById("minimize-circle").addEventListener("mouseenter", function () {
      this.style.opacity = "0.7";
    });
    document.getElementById("minimize-circle").addEventListener("mouseleave", function () {
      this.style.opacity = "1";
    });

    // Submit on Enter (not Shift+Enter)
    const textarea = document.getElementById("message");
    const form = document.getElementById("chatbox-form");
    textarea.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        form.submit();
      }
    });

    // Smart autoscroll
    const panel = document.getElementById("chatbox-panel");
    let shouldAutoScroll = true;
    function isScrolledToBottom() {
      return Math.abs(panel.scrollHeight - panel.scrollTop - panel.clientHeight) < 10;
    }
    panel.addEventListener("scroll", function () {
      shouldAutoScroll = isScrolledToBottom();
    });
    function updateScroll() {
      if (shouldAutoScroll) {
        panel.scrollTop = panel.scrollHeight;
      }
    }
    // Initial scroll
    updateScroll();

    // Chatbox streaming code
    const evtSource = new EventSource("/services/?&user_id=<?= $_SESSION['user_id'] ?>");
    evtSource.addEventListener("ping", (event) => {
      const eventList = document.getElementById("chatbox-messages");
      const data = JSON.parse(event.data);
      const message = data.message;
      const sender = data.sender;
      const pos = data.pos;

      let holder = document.createElement("div");
      let avatar = document.createElement("img");
      avatar.src = sender;
      avatar.className = "chat-avatar";
      avatar.alt = "Avatar";
      let newline = document.createElement("div");
      newline.className = "break";
      let bubble = document.createElement("div");
      bubble.className = "chat-bubble";
      bubble.textContent = message;

      if (pos === 'right') {
        holder.className = "chat-item iright";
        // bubble first, then avatar
        holder.appendChild(bubble);
        holder.appendChild(avatar);
      } else {
        holder.className = "chat-item";
        // avatar first, then bubble
        holder.appendChild(avatar);
        holder.appendChild(bubble);
      }
      eventList.appendChild(holder);
      eventList.appendChild(newline);
      updateScroll();
    });
  });
</script>