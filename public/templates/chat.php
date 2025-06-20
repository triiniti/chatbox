<?php $this->layout('template', ['title' => 'Chatbox']) ?>

<style>
  body,
  html {
    height: 100%;
    margin: 0;
    background: #f0f2f5;
    font-family: 'Segoe UI', Arial, sans-serif;
  }

  .container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .chatbox-card {
    width: 100%;
    max-width: 450px;
    min-height: 70vh;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08), 0 1.5px 4px rgba(44, 62, 80, 0.06);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s;
    position: relative;
  }

  .top-bar {
    background: #f5f5f5;
    height: 36px;
    display: flex;
    align-items: center;
    padding: 0 16px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    border-bottom: 1px solid #eee;
  }

  .circles {
    display: flex;
    gap: 8px;
  }

  .circle {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
    cursor: pointer;
    border: 1px solid #ddd;
    transition: opacity 0.15s;
  }

  #close-circle {
    background: #ff5f56;
  }

  #minimize-circle {
    background: #ffbd2e;
  }

  #maximize-circle {
    background: #27c93f;
  }

  .content {
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    padding: 0;
    min-height: 0;
  }

  .chatbox-panel {
    flex: 1 1 0;
    min-height: 250px;
    background: #fafafa;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-bottom: 1px solid #f0f2f5;
  }

  .chat-item {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    margin-bottom: 0;
  }

  .chat-item.right {
    flex-direction: row-reverse;
  }

  .chat-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 1px 3px rgba(44, 62, 80, 0.09);
  }

  .chat-bubble {
    max-width: 70%;
    padding: 8px 16px;
    border-radius: 16px;
    background: #e3f2fd;
    font-size: 1rem;
    word-break: break-word;
    margin-bottom: 2px;
  }

  .chat-item.right .chat-bubble {
    background: #d1ffd6;
    text-align: right;
  }

  .chatbox-input-area {
    background: #f5f5f5;
    padding: 12px;
    display: flex;
    gap: 10px;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
  }

  .chatbox-input {
    flex: 1 1 0;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 1rem;
    resize: none;
    min-height: 38px;
    background: #fff;
    color: #000;
    outline: none;
    transition: border 0.2s;
  }

  .chatbox-input:focus {
    border-color: #111;
  }

  .btn-send {
    background: #111;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0 18px;
    min-width: 48px;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    transition: background 0.2s;
  }

  .btn-send:hover {
    background: #333;
  }

  .chatbox-footer {
    text-align: center;
    padding: 8px 0;
    background: none;
  }

  .btn-logout {
    background: none;
    color: #888;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: underline;
    margin-top: 6px;
    transition: color 0.2s;
  }

  .btn-logout:hover {
    color: #d32f2f;
  }

  .chatbox-maximized {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    display: flex !important;
    flex-direction: column;
  }

  .no-scroll {
    overflow: hidden !important;
  }

  @media (max-width: 600px) {
    .chatbox-card {
      max-width: 100vw;
      border-radius: 0;
    }

    .content {
      min-height: 90vh;
    }
  }
</style>

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
              <div class="chat-item right">
                <img src="<?= $message['avatar'] ?>" class="chat-avatar" alt="Avatar">
                <div class="chat-bubble"><?= $message['content'] ?></div>
              </div>
            <?php else: ?>
              <div class="chat-item">
                <img src="<?= $message['avatar'] ?>" class="chat-avatar" alt="Avatar">
                <div class="chat-bubble"><?= $message['content'] ?></div>
              </div>
            <?php endif; ?>
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
      card.classList.toggle("chatbox-maximized");
      document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
    });

    document.getElementById("minimize-circle").addEventListener("click", function () {
      const card = document.getElementById("chatbox-card");
      card.classList.toggle("chatbox-maximized");
      document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
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

    // Chatbox streaming code (unchanged)
    const evtSource = new EventSource("/services/");
    evtSource.addEventListener("ping", (event) => {
      const eventList = document.getElementById("chatbox-messages");
      const data = JSON.parse(event.data);
      const message = data.message;
      const sender = data.sender;
      const pos = data.pos;

      if (pos === 'right') {
        const holder = document.createElement("div");
        holder.className = "chat-item right";
        const avatar = document.createElement("img");
        avatar.src = sender;
        avatar.className = "chat-avatar";
        avatar.alt = "Avatar";
        const bubble = document.createElement("div");
        bubble.className = "chat-bubble";
        bubble.textContent = message;
        holder.appendChild(avatar);
        holder.appendChild(bubble);
        eventList.appendChild(holder);
      } else {
        const holder = document.createElement("div");
        holder.className = "chat-item";
        const avatar = document.createElement("img");
        avatar.src = sender;
        avatar.className = "chat-avatar";
        avatar.alt = "Avatar";
        const bubble = document.createElement("div");
        bubble.className = "chat-bubble";
        bubble.textContent = message;
        holder.appendChild(avatar);
        holder.appendChild(bubble);
        eventList.appendChild(holder);
      }
      updateScroll();
    });
  });
</script>