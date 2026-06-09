/**
 * Dashboard UI Render Engine
 * Manages DOM updates, events mapping, charts, animations, and transitions.
 */

let chartInstance = null;

export const dashboardUi = {
  /**
   * Helper: Show Toast Notification
   */
  showToast(message, type = "info") {
    const root = document.getElementById("toast-root");
    if (!root) return;

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    
    let icon = "fa-info-circle";
    if (type === "success") icon = "fa-check-circle";
    if (type === "error") icon = "fa-triangle-exclamation";

    toast.innerHTML = `
      <i class="fa-solid ${icon}"></i>
      <span style="font-size: 13px; font-weight: 500;">${message}</span>
      <span class="toast-close"><i class="fa-solid fa-xmark"></i></span>
    `;

    root.appendChild(toast);

    // Close on click
    toast.querySelector(".toast-close").addEventListener("click", () => {
      toast.remove();
    });

    // Auto remove
    setTimeout(() => {
      if (toast.parentNode) {
        toast.style.animation = "slideIn var(--transition-fast) reverse";
        setTimeout(() => toast.remove(), 200);
      }
    }, 4000);
  },

  /**
   * Helper: Format Date strings nicely
   */
  formatDate(isoString) {
    if (!isoString) return "";
    const date = new Date(isoString);
    return date.toLocaleDateString("en-US", { month: "short", day: "numeric", hour: "2-digit", minute: "2-digit" });
  },

  /**
   * Helper: Show loading spinner in container
   */
  showSpinner(container) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="spinner"></div>
        <p style="font-size: 13px; color: var(--text-muted);">Fetching live feeds...</p>
      </div>
    `;
  },

  /**
   * Helper: Show empty state card in container
   */
  showEmptyState(container, iconClass, message) {
    container.innerHTML = `
      <div class="empty-state">
        <i class="${iconClass}"></i>
        <p style="font-size: 14px; font-weight: 500;">${message}</p>
      </div>
    `;
  },

  /**
   * Render Chart.js Analytics Timeseries
   */
  renderInsightsChart(canvasId, timeseries) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (chartInstance) {
      chartInstance.destroy();
    }

    const labels = timeseries.map((t, idx) => {
      const d = t.date || t.timestamp || t.created_at || t.time || (t.stats && (t.stats.date || t.stats.timestamp || t.stats.created_at || t.stats.time));
      if (d) {
        const dateObj = new Date(d);
        if (!isNaN(dateObj.getTime())) {
          return dateObj.toLocaleDateString([], { month: '2-digit', day: '2-digit' });
        }
        return d;
      }
      return `Snapshot ${idx + 1}`;
    });
    const reachData = timeseries.map(t => {
      const s = t.stats || t;
      return s.reach_1d ?? s.reach ?? s.reach_30d ?? s.total_reach ?? 0;
    });
    const viewsData = timeseries.map(t => {
      const s = t.stats || t;
      return s.profile_views_1d ?? s.profile_views ?? s.profile_views_30d ?? s.views ?? s.view_count ?? 0;
    });
    const followersData = timeseries.map(t => {
      const s = t.stats || t;
      return s.follower_count_1d ?? s.followers_count ?? s.follower_count ?? s.followers ?? s.total_followers ?? 0;
    });

    chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Reach (Daily)',
            data: reachData,
            borderColor: '#dd2a7b', // Pink
            backgroundColor: 'rgba(221, 42, 123, 0.1)',
            borderWidth: 2,
            tension: 0.3,
            yAxisID: 'y'
          },
          {
            label: 'Profile Views',
            data: viewsData,
            borderColor: '#f85f29', // Orange
            backgroundColor: 'rgba(248, 95, 41, 0.1)',
            borderWidth: 2,
            tension: 0.3,
            yAxisID: 'y'
          },
          {
            label: 'Followers Growth',
            data: followersData,
            borderColor: '#38bdf8', // Blue
            backgroundColor: 'transparent',
            borderWidth: 2,
            borderDash: [5, 5],
            tension: 0.1,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: {
              color: '#94a3b8',
              font: { family: 'Inter', size: 12 }
            }
          }
        },
        scales: {
          x: {
            grid: { color: 'rgba(255,255,255,0.03)' },
            ticks: { color: '#64748b' }
          },
          y: {
            position: 'left',
            grid: { color: 'rgba(255,255,255,0.03)' },
            ticks: { color: '#64748b' },
            title: { display: true, text: 'Reach / Views', color: '#64748b' }
          },
          y1: {
            position: 'right',
            grid: { drawOnChartArea: false },
            ticks: { color: '#64748b' },
            title: { display: true, text: 'Total Followers', color: '#64748b' }
          }
        }
      }
    });
  },

  /**
   * Render Recent Post Cards
   */
  renderPostAnalytics(container, posts) {
    if (!posts || posts.length === 0) {
      this.showEmptyState(container, "fa-solid fa-table-cells", "No posts found. Use the Publisher tab to create one!");
      return;
    }

    container.innerHTML = "";
    posts.forEach(post => {
      const isVideo = post.media && post.media[0] && (post.media[0].endsWith(".mp4") || post.media[0].endsWith(".mov") || post.media[0].includes("mixkit.co"));
      const mediaHtml = isVideo 
        ? `<video src="${post.media[0]}" muted loop></video>`
        : `<img src="${post.media[0] || 'https://picsum.photos/id/102/400/400'}" alt="Preview" onerror="this.src='https://picsum.photos/id/102/400/400'">`;

      let iconFormat = "fa-table-cells";
      if (post.format === "reel") iconFormat = "fa-clapperboard";
      if (post.format === "story") iconFormat = "fa-circle-notch";

      const rawStats = post.stats || {};
      const impressions = rawStats.impressions ?? rawStats.impression_count ?? rawStats.views ?? rawStats.view_count ?? rawStats.reach ?? 0;
      const likes = rawStats.likes ?? rawStats.like_count ?? rawStats.likes_count ?? 0;
      const comments = rawStats.comments ?? rawStats.comment_count ?? rawStats.comments_count ?? 0;

      const card = document.createElement("div");
      card.className = "post-analytics-card";
      card.innerHTML = `
        <div class="post-card-preview">
          ${mediaHtml}
          <div class="post-format-badge">
            <i class="fa-solid ${iconFormat}"></i> ${post.format}
          </div>
        </div>
        <div class="post-card-body">
          <div class="post-card-caption">${post.body || "(Story / Direct Media)"}</div>
          <div class="post-card-date">${this.formatDate(post.created_at)}</div>
          
          <div class="post-card-stats">
            <div class="card-stat-item">
              <span class="card-stat-label">Impressions</span>
              <span class="card-stat-val">${Number(impressions).toLocaleString()}</span>
            </div>
            <div class="card-stat-item">
              <span class="card-stat-label">Likes</span>
              <span class="card-stat-val">${Number(likes).toLocaleString()}</span>
            </div>
            <div class="card-stat-item">
              <span class="card-stat-label">Comments</span>
              <span class="card-stat-val">${Number(comments).toLocaleString()}</span>
            </div>
          </div>
        </div>
      `;

      // Auto play video previews on hover
      if (isVideo) {
        const video = card.querySelector("video");
        card.addEventListener("mouseenter", () => video.play().catch(() => {}));
        card.addEventListener("mouseleave", () => {
          video.pause();
          video.currentTime = 0;
        });
      }

      container.appendChild(card);
    });
  },

  /**
   * Render Chat Inbox List
   */
  renderChatsList(container, chats, activeChatId, onSelectChat) {
    if (!chats || chats.length === 0) {
      this.showEmptyState(container, "fa-solid fa-paper-plane", "No active conversations found.");
      return;
    }

    container.innerHTML = "";
    chats.forEach(chat => {
      const activeClass = chat.chat_id === activeChatId ? "active" : "";
      const initials = chat.participant_username ? chat.participant_username.slice(0, 2) : "UN";
      
      const lastTimeVal = chat.last_message_at || 
                          chat.last_inbound_at || 
                          chat.last_outbound_at || 
                          chat.last_message_time || 
                          chat.updated_at || 
                          (chat.last_message && typeof chat.last_message === "object" && (chat.last_message.timestamp || chat.last_message.created_at || chat.last_message.sent_at)) || 
                          chat.timestamp || 
                          chat.created_at;

      let lastMsgText = "(Media Attachment)";
      if (chat.last_message) {
        if (typeof chat.last_message === "object") {
          lastMsgText = chat.last_message.body || chat.last_message.text || chat.last_message.message || "(Media Attachment)";
        } else {
          lastMsgText = chat.last_message;
        }
      }

      let timeStr = "";
      if (lastTimeVal) {
        const d = new Date(lastTimeVal);
        if (!isNaN(d.getTime())) {
          timeStr = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
      }
      
      const item = document.createElement("div");
      item.className = `chat-item ${activeClass}`;
      item.innerHTML = `
        <div class="chat-item-avatar">${initials}</div>
        <div class="chat-item-content">
          <div class="chat-item-header">
            <div class="chat-item-username">@${chat.participant_username || 'anonymous'}</div>
            <div class="chat-item-time">${timeStr}</div>
          </div>
          <div class="chat-item-lastmsg">${lastMsgText}</div>
        </div>
      `;

      item.addEventListener("click", () => onSelectChat(chat.chat_id));
      container.appendChild(item);
    });
  },

  /**
   * Render Messages in conversation
   */
  renderConversation(container, messages, profileId, onReact) {
    if (!messages || messages.length === 0) {
      this.showEmptyState(container, "fa-solid fa-message", "Start of conversation. Send a message to open the chat.");
      return;
    }

    container.innerHTML = "";
    messages.forEach(msg => {
      const isOutbound = msg.sender === "outbound" || 
                         msg.sender === "me" ||
                         msg.direction === "outbound" || 
                         msg.is_outbound === true || 
                         msg.outbound === true ||
                         msg.from_me === true ||
                         (profileId && msg.sender_id === profileId) ||
                         (profileId && msg.sender === profileId);
      const rowClass = isOutbound ? "outbound" : "inbound";
      
      const row = document.createElement("div");
      row.className = `message-row ${rowClass}`;
      
      let attachmentHtml = "";
      if (msg.attachment) {
        attachmentHtml = `
          <div class="attachment-preview">
            ${msg.attachment.type === 'story_mention' ? '<span class="story-mention-badge"><i class="fa-solid fa-star"></i> Story Mention</span>' : ''}
            <img src="${msg.attachment.url}" alt="Media attachment" onerror="this.parentNode.innerHTML='<span style=\'padding: 8px; font-size:12px; color:var(--text-muted)\'>Failed to load attachment</span>'">
          </div>
        `;
      }

      // Reactions
      let reactionHtml = "";
      const heartReact = msg.reactions && msg.reactions.find(r => r.reaction === "love");
      if (heartReact) {
        reactionHtml = `
          <div class="reaction-container">
            <span class="reaction-tag" data-msg-id="${msg.id}">❤️</span>
          </div>
        `;
      }

      const noteHtml = msg.note ? `<div style="font-size: 10px; color: var(--insta-yellow); margin-bottom: 2px;"><i class="fa-solid fa-lock"></i> ${msg.note}</div>` : '';

      let timeStr = "Recently";
      const rawTime = msg.timestamp || msg.created_at || msg.sent_at || msg.time;
      if (rawTime) {
        const parsedDate = new Date(rawTime);
        if (!isNaN(parsedDate.getTime())) {
          timeStr = parsedDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
      }

      row.innerHTML = `
        ${attachmentHtml}
        ${noteHtml}
        <div class="message-bubble">
          ${msg.body || ""}
          <button class="reaction-trigger-btn" data-msg-id="${msg.id}" title="React love"><i class="fa-regular fa-heart"></i></button>
        </div>
        <div class="message-meta">
          <span>${timeStr}</span>
          ${isOutbound ? '<i class="fa-solid fa-check-double" style="color:var(--success-color)"></i>' : ''}
        </div>
        ${reactionHtml}
      `;

      // Set up listeners for reactions
      const heartBtn = row.querySelector(".reaction-trigger-btn");
      if (heartBtn) {
        heartBtn.addEventListener("click", () => onReact(msg.id, "love"));
      }

      const tag = row.querySelector(".reaction-tag");
      if (tag) {
        tag.addEventListener("click", () => onReact(msg.id, "love"));
      }

      container.appendChild(row);
    });

    // Auto-scroll to bottom
    setTimeout(() => {
      container.scrollTop = container.scrollHeight;
    }, 50);
  },

  /**
   * Render Comment post sidebar list
   */
  renderCommentPostsList(container, posts, activePostId, onSelectPost) {
    if (!posts || posts.length === 0) {
      container.innerHTML = `<div style="padding: 16px; font-size:12px; color:var(--text-muted); text-align:center;">No posts available</div>`;
      return;
    }

    container.innerHTML = "";
    posts.forEach(post => {
      const activeClass = post.id === activePostId ? "active" : "";
      
      // Select appropriate icon
      let icon = "fa-image";
      if (post.format === "reel") icon = "fa-clapperboard";
      if (post.format === "story") icon = "fa-circle-notch";

      const item = document.createElement("div");
      item.className = `comments-post-item ${activeClass}`;
      item.innerHTML = `
        <div class="post-thumb">
          ${post.media && post.media[0] && !post.media[0].endsWith(".mp4")
            ? `<img src="${post.media[0]}" alt="Post Thumb">`
            : `<i class="fa-solid ${icon}" style="font-size: 18px; color: var(--text-muted)"></i>`
          }
        </div>
        <div class="post-info-desc">
          <div class="post-info-caption">${post.body || "(Story / Video)"}</div>
          <div class="post-info-meta">ID: ${(post.id || "").slice(0, 10)}...</div>
        </div>
      `;

      item.addEventListener("click", () => onSelectPost(post.id));
      container.appendChild(item);
    });
  },

  /**
   * Render Comments Stream and Thread replies
   */
  renderCommentsStream(container, comments, onAction) {
    if (!comments || comments.length === 0) {
      this.showEmptyState(container, "fa-regular fa-comments", "No comments on this post yet.");
      return;
    }

    container.innerHTML = "";
    comments.forEach(comment => {
      const isHidden = comment.hidden;
      const card = document.createElement("div");
      card.className = `comment-card ${isHidden ? 'hidden-comment' : ''}`;
      
      const commenterUsername = comment.username || comment.author_username || (comment.from && comment.from.username) || "anonymous";
      const initials = commenterUsername.slice(0, 2);
      const verifiedHtml = comment.is_verified_user ? '<i class="fa-solid fa-circle-check badge-verified" title="Verified Creator"></i>' : '';
      const followsHtml = comment.is_user_follow_business ? '<span class="badge-follows">Follows You</span>' : '';
      const isHiddenText = isHidden ? '<span style="color:var(--danger-color); font-size:11px; font-weight:700; margin-left:4px;">[HIDDEN]</span>' : '';

      card.innerHTML = `
        <div class="comment-card-header">
          <div class="commenter-info">
            <div class="commenter-avatar">${initials}</div>
            <div class="commenter-username">@${commenterUsername}</div>
            ${verifiedHtml}
            ${followsHtml}
            ${isHiddenText}
          </div>
          <div class="comment-time">${this.formatDate(comment.created_at)}</div>
        </div>
        <div class="comment-body">${comment.body}</div>
        
        <div class="comment-actions">
          <button class="comment-action-btn btn-reply" data-action="reply" data-id="${comment.id}">
            <i class="fa-solid fa-reply"></i> Reply
          </button>
          <button class="comment-action-btn btn-hide" data-action="toggle-hide" data-id="${comment.id}">
            <i class="fa-solid ${isHidden ? 'fa-eye' : 'fa-eye-slash'}"></i> ${isHidden ? 'Unhide' : 'Hide'}
          </button>
          <button class="comment-action-btn btn-delete" data-action="delete" data-id="${comment.id}">
            <i class="fa-solid fa-trash"></i> Delete
          </button>
          <button class="comment-action-btn btn-private" data-action="private-dm" data-id="${comment.id}">
            <i class="fa-solid fa-envelope"></i> Private Reply (DM)
          </button>
        </div>

        <!-- Render nested replies -->
        <div class="comment-replies-list" id="replies-container-${comment.id}"></div>
      `;

      // Render replies nested
      const repliesContainer = card.querySelector(`#replies-container-${comment.id}`);
      if (comment.replies && comment.replies.length > 0) {
        comment.replies.forEach(reply => {
          const replyUsername = reply.username || reply.author_username || (reply.from && reply.from.username) || "anonymous";
          const replyInitials = replyUsername.slice(0, 2);
          const replyCard = document.createElement("div");
          replyCard.className = "reply-card";
          replyCard.innerHTML = `
            <div class="comment-card-header" style="margin-bottom: 4px;">
              <div class="commenter-info">
                <div class="commenter-avatar" style="width:20px; height:20px; font-size:9px;">${replyInitials}</div>
                <div class="commenter-username" style="font-size:12px;">@${replyUsername}</div>
              </div>
              <div class="comment-time" style="font-size:10px;">${this.formatDate(reply.created_at)}</div>
            </div>
            <div class="comment-body" style="font-size:13px; margin-bottom: 4px;">${reply.body}</div>
            <div class="comment-actions">
              <button class="comment-action-btn btn-delete" data-action="delete" data-id="${reply.id}">
                <i class="fa-solid fa-trash" style="font-size:10px;"></i> Delete Reply
              </button>
            </div>
          `;
          
          replyCard.querySelector(".btn-delete").addEventListener("click", () => {
            onAction("delete", reply.id, comment.username);
          });
          repliesContainer.appendChild(replyCard);
        });
      }

      // Attach action listeners
      card.querySelectorAll("[data-action]").forEach(btn => {
        btn.addEventListener("click", () => {
          const action = btn.getAttribute("data-action");
          const cId = btn.getAttribute("data-id");
          onAction(action, cId, comment.username);
        });
      });

      container.appendChild(card);
    });
  },

  /**
   * Update Live Interactive Publisher Preview Panel
   */
  updatePublisherPreview(format, caption, mediaUrl) {
    const clock = document.getElementById("preview-phone-clock");
    const now = new Date();
    clock.innerText = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

    const phoneBody = document.getElementById("phone-body-preview");
    const mediaContainer = document.getElementById("phone-media-container");
    const phoneCaption = document.getElementById("phone-caption-text");

    // Format mode layouts
    if (format === "story") {
      phoneBody.className = "phone-body preview-story-mode";
    } else {
      phoneBody.className = "phone-body";
    }

    // Media content render
    if (!mediaUrl) {
      mediaContainer.innerHTML = `<i class="fa-solid fa-image" style="font-size: 48px; color: var(--bg-tertiary);"></i>`;
    } else {
      const urls = mediaUrl.split(",").map(u => u.trim()).filter(Boolean);
      const firstUrl = urls[0];
      const isVideo = firstUrl.endsWith(".mp4") || firstUrl.endsWith(".mov") || firstUrl.includes("mixkit.co");

      if (isVideo) {
        mediaContainer.innerHTML = `<video src="${firstUrl}" autoplay muted loop style="width:100%; height:100%; object-fit:cover;"></video>`;
      } else {
        mediaContainer.innerHTML = `<img src="${firstUrl}" alt="Preview Image" style="width:100%; height:100%; object-fit:cover;" onerror="this.parentNode.innerHTML='<span style=\'font-size:12px; color:var(--text-muted)\'>Invalid Media Link</span>'">`;
      }
    }

    // Caption
    phoneCaption.innerText = caption || "Your post caption will appear here...";
  },

  /**
   * Render Notifications List
   */
  renderNotificationsList(container, notifications) {
    if (!notifications || notifications.length === 0) {
      container.innerHTML = `
        <div style="text-align: center; padding: 32px; color: var(--text-muted);">
          <i class="fa-regular fa-bell-slash" style="font-size: 36px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
          No notifications recorded.
        </div>
      `;
      return;
    }

    container.innerHTML = notifications.map(notif => {
      let icon = "fa-bell";
      let bg = "rgba(56, 189, 248, 0.1)";
      let color = "var(--accent-color)";
      
      if (notif.type === "message") {
        icon = "fa-envelope";
        bg = "rgba(236, 72, 153, 0.1)";
        color = "#dd2a7b";
      } else if (notif.type === "comment") {
        icon = "fa-comment";
        bg = "rgba(248, 95, 41, 0.1)";
        color = "#f85f29";
      } else if (notif.type === "stat") {
        icon = "fa-chart-line";
        bg = "rgba(56, 189, 248, 0.1)";
        color = "#38bdf8";
      }

      return `
        <div class="notification-item" style="display: flex; gap: 16px; padding: 14px; background: rgba(255,255,255,0.02); border-radius: var(--radius-md); border: var(--glass-border); align-items: flex-start; transition: var(--transition-fast);">
          <div class="notification-icon" style="color: ${color}; font-size: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: ${bg}; border-radius: 50%; flex-shrink: 0;">
            <i class="fa-solid ${icon}"></i>
          </div>
          <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; flex-wrap: wrap; gap: 4px;">
              <strong style="font-size: 14px; color: #fff;">${escapeHtml(notif.title)}</strong>
              <span style="font-size: 11px; color: var(--text-muted);">${notif.time}</span>
            </div>
            <div style="font-size: 13px; color: var(--text-secondary);">${escapeHtml(notif.message)}</div>
          </div>
        </div>
      `;
    }).reverse().join("");
  }
};

// Global function to render API logs in the UI
window.updateApiLogsUi = function() {
  const container = document.getElementById("api-debugger-logs");
  if (!container) return;

  if (!window.apiLogs || window.apiLogs.length === 0) {
    container.innerHTML = `<div style="color: var(--text-muted);">No API transactions recorded yet. Switch to Live mode or trigger a Test Connection.</div>`;
    return;
  }

  container.innerHTML = window.apiLogs.map(log => {
    const isError = log.status === "ERROR" || (typeof log.status === "number" && log.status >= 400);
    const color = isError ? "var(--danger-color)" : "var(--success-color)";
    let shortResponse = log.response;
    if (shortResponse && shortResponse.length > 500) {
      shortResponse = shortResponse.substring(0, 500) + "... (truncated)";
    }
    
    try {
      const parsed = JSON.parse(log.response);
      shortResponse = JSON.stringify(parsed, null, 2);
    } catch (e) {
      // keep raw string
    }

    return `
      <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-family: monospace;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
          <span>[${log.timestamp}] <strong style="color: ${color}">${log.method}</strong> <span style="color: var(--text-secondary); word-break: break-all;">${log.url}</span></span>
          <span style="font-weight: bold; color: ${color}">Status: ${log.status}</span>
        </div>
        <pre style="background: rgba(0,0,0,0.3); padding: 8px; border-radius: 4px; color: #a1a1aa; word-break: break-all; white-space: pre-wrap; margin: 0; font-size: 11px; max-height: 150px; overflow-y: auto;">${escapeHtml(shortResponse || 'No response body')}</pre>
      </div>
    `;
  }).reverse().join("");
};

function escapeHtml(text) {
  if (!text) return "";
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
