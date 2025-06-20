<?php $this->layout('template', ['title' => 'Chatbox']) ?>

<div class="container" style="height: 100vh; display: flex; align-items: center;">
  <div class="row" style="width: 100%;">
    <div class="col s12 m8 l6 offset-m2 offset-l3">
      <div class="card z-depth-3 chatbox-holder" style="padding: 0; transition: all 0.3s;" id="chatbox-card">
        <div class="card-content" style="padding: 0;">
          <!-- Browser-style Top Bar -->
          <div class='top-bar' style="background: #f5f5f5; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <div class='circles' style="padding: 12px;">
              <div id="close-circle" class="circle red lighten-2" title="Logout"
                style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 6px; cursor:pointer;">
              </div>
              <div id="minimize-circle" class="circle yellow lighten-2" title="Minimize"
                style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 6px; cursor:pointer;">
              </div>
              <div id="maximize-circle" class="circle green lighten-2" title="Maximize"
                style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; cursor:pointer;"></div>
            </div>
          </div>
          <div class='content' style="padding: 0 0 8px 0;">
            <!-- Chatbox Messages -->
            <div class="chatbox-submit">
              <div class="row" style="margin-bottom: 0; width: calc(100% - 24px); margin-left: 12px;">
                <div class="col s12 chatbox-list chatbox-panel z-depth-1" id="chatbox-panel"
                  style="height: 350px; overflow-y: auto; background: #fafafa; border-radius: 8px; padding: 12px;">
                  <div id="chatbox-messages" class="row" style="margin-bottom: 0;">
                    <?php foreach ($messages as $message): ?>
                      <?php if ($message['position'] === 'right'): ?>
                        <div class="chat-item row" style="margin-bottom: 0;">
                          <div class="col s9">
                            <div class="right-align" style="padding: 6px 0;">
                              <span class="flow-text" style="font-size: 1.05rem;"><?= $message['content'] ?></span>
                            </div>
                          </div>
                          <div class="col s3 right-align">
                            <img style="width:40px; height:40px;" src="<?= $message['avatar'] ?>" alt="Avatar"
                              class="circle z-depth-1">
                          </div>
                        </div>
                      <?php else: ?>
                        <div class="chat-item row" style="margin-bottom: 0;">
                          <div class="col s3 left-align">
                            <img style="width:40px; height:40px;" src="<?= $message['avatar'] ?>" alt="Avatar"
                              class="circle z-depth-1">
                          </div>
                          <div class="col s9">
                            <div class="left-align" style="padding: 6px 0;">
                              <span class="flow-text" style="font-size: 1.05rem;"><?= $message['content'] ?></span>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              <!-- Chatbox Input -->
              <form name="chatbox-form" action="/chat" method="POST" id="chatbox-form" style="margin-bottom: 0;">
                <div class="chatbox-input" style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 0;">
                  <?= $csrf_token ?>
                  <div class="input-field" style="flex: 1; margin-bottom: 0;">
                    <textarea name="message" id="message" class="materialize-textarea" required
                      style="background-color: #fff; border-radius: 4px; min-height: 38px; padding-left: 8px;"></textarea>
                    <label for="message" class="active">Type your message…</label>
                  </div>
                  <button class="btn waves-effect waves-light black white-text" type="submit" name="action"
                    style="min-width: 50px; height: 44px; display: flex; align-items: center; justify-content: center;">
                    <i class="material-icons right">send</i>
                  </button>
                </div>
                <div class="row" style="margin-bottom: 0;">
                  <div class="col s12 center-align" style="padding-bottom: 6px;">
                    <a href="/logout" class="waves-effect waves-light btn-flat grey-text text-darken-2"
                      style="text-transform: none;">
                      <i class="material-icons left">exit_to_app</i>Logout
                    </a>
                  </div>
                </div>
              </form>
              <!-- End Chatbox Input -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Topbar buttons
    document.getElementById("close-circle").addEventListener("click", function () {
      window.location.href = "/logout";
    });

    document.getElementById("maximize-circle").addEventListener("click", function () {
      const card = document.getElementById("chatbox-card");
      card.classList.toggle("chatbox-maximized");
      // Optionally toggle overflow on body to prevent scrolling when maximized
      document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
    });

    document.getElementById("minimize-circle").addEventListener("click", function () {
      const card = document.getElementById("chatbox-card");
      card.classList.toggle("chatbox-maximized");
      // Optionally toggle overflow on body to prevent scrolling when maximized
      document.body.classList.toggle("no-scroll", card.classList.contains("chatbox-maximized"));
    });

    // Optionally add a hover effect to yellow button, but no click action
    document.getElementById("minimize-circle").addEventListener("mouseenter", function () {
      this.style.opacity = "0.7";
    });
    document.getElementById("minimize-circle").addEventListener("mouseleave", function () {
      this.style.opacity = "1";
    });

    const textarea = document.getElementById("message");
    const form = document.getElementById("chatbox-form");
    textarea.addEventListener("keydown", function (e) {
      // Enter without shift submits form
      if (e.key === "Enter" && !e.shiftKey) {
        console.log("Submitting form");
        // Prevent default behavior of adding a new line  
        e.preventDefault();
        form.submit();
      }
    });

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
        holder.className = "chat-item row";
        const textholder = document.createElement("div");
        textholder.className = "col s9";
        const paragraph = document.createElement("div");
        paragraph.className = "right-align";
        paragraph.style.padding = "6px 0";
        const span = document.createElement("span");
        span.className = "flow-text";
        span.style.fontSize = "1.05rem";
        span.textContent = message;
        paragraph.appendChild(span);
        textholder.appendChild(paragraph);
        holder.appendChild(textholder);
        const imageholder = document.createElement("div");
        imageholder.className = "col s3 right-align";
        const avatar = document.createElement("img");
        avatar.src = sender;
        avatar.style.width = "40px";
        avatar.style.height = "40px";
        avatar.className = "circle z-depth-1";
        imageholder.appendChild(avatar);
        holder.appendChild(imageholder);
        eventList.appendChild(holder);
      } else {
        const holder = document.createElement("div");
        holder.className = "chat-item row";
        const imageholder = document.createElement("div");
        imageholder.className = "col s3 left-align";
        const avatar = document.createElement("img");
        avatar.src = sender;
        avatar.style.width = "40px";
        avatar.style.height = "40px";
        avatar.className = "circle z-depth-1";
        imageholder.appendChild(avatar);
        holder.appendChild(imageholder);
        const textholder = document.createElement("div");
        textholder.className = "col s9";
        const paragraph = document.createElement("div");
        paragraph.className = "left-align";
        paragraph.style.padding = "6px 0";
        const span = document.createElement("span");
        span.className = "flow-text";
        span.style.fontSize = "1.05rem";
        span.textContent = message;
        paragraph.appendChild(span);
        textholder.appendChild(paragraph);
        holder.appendChild(textholder);
        eventList.appendChild(holder);
      }

      updateScroll();
    });

    function updateScroll() {
      var element = document.getElementById("chatbox-panel");
      element.scrollTop = element.scrollHeight;
    }
    updateScroll();
  });
</script>

<style>
  /* Maximized style for chatbox */
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
    transition: all 0.3s;
    display: flex !important;
    flex-direction: column;
  }

  .no-scroll {
    overflow: hidden !important;
  }
</style>