/**
 * Core Application Controller
 * Manages state, event bindings, local cache, and branches between Demo/Live API engines.
 */

import { mockDb } from './mockData.js';
import { haloocomApi } from './api.js';
import { dashboardUi } from './ui.js';

// Helper to guarantee an array from API responses which may wrap lists in an object key
function normalizeArray(val, fallbackKey) {
  if (Array.isArray(val)) return val;
  if (val && typeof val === "object") {
    if (Array.isArray(val.data)) return val.data;
    if (fallbackKey && Array.isArray(val[fallbackKey])) return val[fallbackKey];
    // Search for any array property
    for (const key in val) {
      if (Array.isArray(val[key])) {
        return val[key];
      }
    }
  }
  return [];
}

// Application State
const state = {
  mode: "live", // strictly "live"
  apiKey: "",
  profileId: "",
  activeChatId: null,
  activePostId: null,
  chats: [],
  posts: [],
  comments: [],
  replyingToCommentId: null,
  replyingToUsername: null,
  customPostIds: [], // User-loaded posts in Live Mode
  notifications: [],
  unreadMessages: 0,
  unreadComments: 0,
  seenChatTimes: {}, // chat_id -> last_message_time
  seenCommentIds: new Set(),
  polledPostIds: new Set(), // Track posts that have been polled
  previousFollowers: null,
  activeTab: "dashboard" // Track active tab
};

// LocalStorage Keys
const KEYS = {
  API_KEY: "haloocom_api_key",
  PROFILE_ID: "haloocom_profile_id",
  MODE: "haloocom_mode",
  PUBLISHED_POSTS: "haloocom_published_posts"
};

/**
 * Initialize Application
 */
document.addEventListener("DOMContentLoaded", () => {
  loadCachedSettings();
  initNavigation();
  initModeSwitcher();
  initCredentialsForm();
  initDirectMessages();
  initCommentsManager();
  initPublisher();
  initNotificationsManager();
  
  // Initial data fetch
  refreshData();

  // Start background polling
  startBackgroundPolling();
});

/**
 * Load settings cached in LocalStorage
 */
function loadCachedSettings() {
  state.apiKey = localStorage.getItem(KEYS.API_KEY) || "";
  state.profileId = localStorage.getItem(KEYS.PROFILE_ID) || "";
  state.mode = "live"; // Force Live mode
  
  // Hydrate input fields
  document.getElementById("input-api-key").value = state.apiKey;
  document.getElementById("input-profile-id").value = state.profileId;

  // Set active mode buttons
  updateModeVisuals();
}

/**
 * Update active state classes on top bar and sidebar indicators
 */
function updateModeVisuals() {
  const statusIndicator = document.getElementById("status-indicator");
  const statusLabel = document.getElementById("status-label");
  const sidebarAvatar = document.getElementById("sidebar-avatar");
  const sidebarName = document.getElementById("sidebar-name");
  const sidebarHandle = document.getElementById("sidebar-handle");

  if (statusIndicator) {
    statusIndicator.className = "status-dot connected";
  }
  if (statusLabel) {
    statusLabel.innerText = "Live Connected";
  }

  // Set layout placeholders
  if (sidebarAvatar) {
    sidebarAvatar.src = "https://picsum.photos/id/111/100/100";
  }
  if (sidebarName) {
    sidebarName.innerText = "Instagram Professional";
  }
  if (sidebarHandle) {
    sidebarHandle.innerText = state.profileId ? `@${state.profileId}` : "@no_profile_id";
  }
}

/**
 * Switch Active View tabs
 */
function initNavigation() {
  const navItems = document.querySelectorAll(".nav-menu .nav-item");
  const views = document.querySelectorAll(".view-panel");
  const title = document.getElementById("current-title");

  navItems.forEach(item => {
    item.addEventListener("click", () => {
      const target = item.getAttribute("data-target");
      
      // Update sidebar
      navItems.forEach(i => i.classList.remove("active"));
      item.classList.add("active");

      // Update active view panel
      views.forEach(v => v.classList.remove("active"));
      const targetView = document.getElementById(`view-${target}`);
      if (targetView) targetView.classList.add("active");

      // Set header title
      state.activeTab = target;
      if (target === "dashboard") title.innerText = "Dashboard & Insights";
      if (target === "messages") title.innerText = "Direct Messages Inbox";
      if (target === "comments") title.innerText = "Comments Moderation";
      if (target === "notifications") title.innerText = "Real-Time Notifications Log";
      if (target === "publisher") title.innerText = "Publish Content Form";
      if (target === "credentials") title.innerText = "Developer Credentials Settings";

      // Refresh view-specific content
      refreshViewData(target);
    });
  });
}

/**
 * Switch Mode engine (Demo vs Live)
 */
function initModeSwitcher() {
  const btnTopTest = document.getElementById("btn-top-test");
  if (btnTopTest) {
    btnTopTest.addEventListener("click", handleTestConnection);
  }
}

/**
 * Handle Test Connection operation
 */
async function handleTestConnection() {
  const btn = document.getElementById("btn-top-test");
  if (!btn) return;
  const originalHtml = btn.innerHTML;
  
  // Live Mode connection test
  if (!state.apiKey || !state.profileId) {
    dashboardUi.showToast("API Key or Profile ID is missing.", "error");
    return;
  }

  try {
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Testing API...`;
    await haloocomApi.testConnection(state.apiKey, state.profileId);
    btn.innerHTML = `<i class="fa-solid fa-circle-check" style="color:var(--success-color)"></i> Connected`;
    dashboardUi.showToast("Successfully authenticated with Haloocom API!", "success");
  } catch (error) {
    btn.innerHTML = `<i class="fa-solid fa-circle-xmark" style="color:var(--danger-color)"></i> Failed`;
    dashboardUi.showToast(`Authentication failed: ${error.message}`, "error");
  } finally {
    setTimeout(() => { btn.innerHTML = originalHtml; }, 4000);
  }
}

/**
 * Credentials View Form Logic
 */
function initCredentialsForm() {
  const btnSave = document.getElementById("btn-save-credentials");
  const btnClear = document.getElementById("btn-clear-credentials");
  
  btnSave.addEventListener("click", async () => {
    const keyVal = document.getElementById("input-api-key").value.trim();
    const profileVal = document.getElementById("input-profile-id").value.trim();

    if (!keyVal || !profileVal) {
      dashboardUi.showToast("Both API Bearer Token and Profile ID are required.", "error");
      return;
    }

    state.apiKey = keyVal;
    state.profileId = profileVal;
    localStorage.setItem(KEYS.API_KEY, keyVal);
    localStorage.setItem(KEYS.PROFILE_ID, profileVal);

    dashboardUi.showToast("Credentials cached successfully!", "success");
    
    updateModeVisuals();
    refreshData();
  });

  btnClear.addEventListener("click", () => {
    document.getElementById("input-api-key").value = "";
    document.getElementById("input-profile-id").value = "";
    state.apiKey = "";
    state.profileId = "";
    localStorage.removeItem(KEYS.API_KEY);
    localStorage.removeItem(KEYS.PROFILE_ID);
    
    dashboardUi.showToast("Credentials cleared successfully.", "info");
    
    updateModeVisuals();
    refreshData();
  });
}

/**
 * Direct Messages Panel Logic
 */
function initDirectMessages() {
  const btnSend = document.getElementById("btn-chat-send");
  const btnRefresh = document.getElementById("btn-chat-refresh");
  const btnAttach = document.getElementById("btn-chat-attachment");
  const textInput = document.getElementById("chat-text-input");

  btnSend.addEventListener("click", handleSendDM);
  textInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") handleSendDM();
  });

  btnRefresh.addEventListener("click", () => {
    if (state.activeChatId) {
      fetchMessages(state.activeChatId);
    }
  });

  // Media Attachment trigger (simply prompts for URL to mimic file picking/attachment URL send)
  btnAttach.addEventListener("click", () => {
    const url = prompt("Enter media attachment URL (image / video link):");
    if (url) {
      state.pendingAttachmentUrl = url;
      btnAttach.classList.add("active");
      dashboardUi.showToast("Media attachment staged!", "info");
    } else {
      state.pendingAttachmentUrl = null;
      btnAttach.classList.remove("active");
    }
  });
}

/**
 * Comments Manager Panel Logic
 */
function initCommentsManager() {
  const btnCloseDrawer = document.getElementById("btn-close-reply-drawer");
  const btnPublicReply = document.getElementById("btn-submit-public-reply");
  const btnPrivateReply = document.getElementById("btn-submit-private-reply");
  const btnCustomPostLoad = document.getElementById("btn-custom-post-load");

  btnCloseDrawer.addEventListener("click", closeReplyDrawer);

  btnPublicReply.addEventListener("click", () => submitCommentReply(false));
  btnPrivateReply.addEventListener("click", () => submitCommentReply(true));

  btnCustomPostLoad.addEventListener("click", () => {
    const customId = document.getElementById("input-custom-post-id").value.trim();
    if (!customId) {
      dashboardUi.showToast("Please enter a valid Post ID.", "error");
      return;
    }
    
    // Save to list of custom post IDs and make active
    if (!state.customPostIds.includes(customId)) {
      state.customPostIds.push(customId);
      // Save local cache of custom post IDs
      localStorage.setItem("haloocom_custom_post_ids", JSON.stringify(state.customPostIds));
    }
    
    state.activePostId = customId;
    document.getElementById("input-custom-post-id").value = "";
    
    dashboardUi.showToast(`Post ${customId} loaded! Loading comments...`, "success");
    fetchComments(customId);
    // Refresh post listing
    loadCommentsSidebar();
  });

  // Reload custom post IDs from cache
  try {
    state.customPostIds = JSON.parse(localStorage.getItem("haloocom_custom_post_ids")) || [];
  } catch (e) {
    state.customPostIds = [];
  }
}

/**
 * Publisher View Logic
 */
function initPublisher() {
  const formatRadios = document.querySelectorAll('input[name="pub-format"]');
  const mediaInput = document.getElementById("pub-media-url");
  const captionInput = document.getElementById("pub-caption");
  const submitBtn = document.getElementById("btn-pub-submit");

  // Format type switcher
  formatRadios.forEach(radio => {
    radio.addEventListener("change", (e) => {
      const selected = e.target.value;
      
      // Toggle visibility of fields based on format
      document.querySelectorAll(".conditional-fields").forEach(el => {
        el.style.display = "none";
      });

      if (selected === "post") {
        document.getElementById("group-pub-caption").style.display = "block";
        document.getElementById("group-pub-first-comment").style.display = "block";
        document.getElementById("group-pub-collaborators").style.display = "block";
        document.getElementById("lbl-pub-media").innerText = "Media URLs (Required)";
        mediaInput.placeholder = "https://example.com/image.jpg (Comma separate up to 10 for carousels)";
      } else if (selected === "reel") {
        document.getElementById("group-pub-caption").style.display = "block";
        document.getElementById("group-pub-first-comment").style.display = "block";
        document.getElementById("group-pub-cover").style.display = "block";
        document.getElementById("group-pub-audio").style.display = "block";
        document.getElementById("group-pub-trial").style.display = "block";
        document.getElementById("group-pub-collaborators").style.display = "block";
        document.getElementById("lbl-pub-media").innerText = "Video URL (Required)";
        mediaInput.placeholder = "https://example.com/video.mp4 (Reels accept 1 video only)";
      } else if (selected === "story") {
        document.getElementById("group-pub-caption").style.display = "none"; // Stories don't have text caption
        document.getElementById("lbl-pub-media").innerText = "Media URL (Required)";
        mediaInput.placeholder = "https://example.com/story.jpg (Stories accept 1 media only)";
      }

      triggerLivePreview();
    });
  });

  // Input listeners for interactive preview
  mediaInput.addEventListener("input", triggerLivePreview);
  captionInput.addEventListener("input", triggerLivePreview);

  // Form submit handler
  submitBtn.addEventListener("click", handlePublishPost);
}

function triggerLivePreview() {
  const format = document.querySelector('input[name="pub-format"]:checked').value;
  const caption = document.getElementById("pub-caption").value;
  const mediaUrl = document.getElementById("pub-media-url").value;
  dashboardUi.updatePublisherPreview(format, caption, mediaUrl);
}

/**
 * Main Controller: Fetch and reload all dashboard view states
 */
function refreshData() {
  refreshViewData("dashboard");
}

function refreshViewData(viewName) {
  if (viewName === "dashboard") {
    fetchStats();
    fetchPosts();
  } else if (viewName === "messages") {
    fetchChats();
    clearMessagesBadge();
  } else if (viewName === "comments") {
    loadCommentsSidebar();
    clearCommentsBadge();
  } else if (viewName === "notifications") {
    const container = document.getElementById("notifications-list-container");
    if (container) {
      dashboardUi.renderNotificationsList(container, state.notifications);
    }
    clearNotificationsBadge();
  } else if (viewName === "publisher") {
    triggerLivePreview();
  } else if (viewName === "credentials") {
    if (window.updateApiLogsUi) window.updateApiLogsUi();
  }
}

/**
 * Fetch Analytics Stats
 */
async function fetchStats() {
  const dashFollowers = document.getElementById("dash-followers");
  const dashReach = document.getElementById("dash-reach");
  const dashEngaged = document.getElementById("dash-engaged");
  const dashClicks = document.getElementById("dash-clicks");

  try {
    if (state.mode === "demo") {
      const stats = await mockDb.getProfileStats();
      dashFollowers.innerText = stats.followers_count.toLocaleString();
      dashReach.innerText = stats.insights.reach_30d.toLocaleString();
      dashEngaged.innerText = stats.insights.accounts_engaged_30d.toLocaleString();
      dashClicks.innerText = stats.insights.website_clicks_30d.toLocaleString();
      
      dashboardUi.renderInsightsChart("insightsChart", stats.timeseries);
    } else {
      // Live Mode
      const stats = await haloocomApi.getProfileStats(state.apiKey, state.profileId);
      console.log("Stats API Raw Response:", stats);
      if (stats) {
        // Extract the latest stats snapshot from various formats (raw array, timeseries property, data property, or root object)
        let latest = null;
        if (stats.data && Array.isArray(stats.data.records) && stats.data.records.length > 0) {
          const records = stats.data.records;
          latest = records[records.length - 1].stats || records[records.length - 1];
        } else if (Array.isArray(stats)) {
          latest = stats[stats.length - 1];
        } else if (stats.timeseries && Array.isArray(stats.timeseries)) {
          latest = stats.timeseries[stats.timeseries.length - 1];
        } else if (stats.data && Array.isArray(stats.data)) {
          latest = stats.data[stats.data.length - 1];
        } else if (stats.data && typeof stats.data === "object") {
          latest = stats.data;
        } else if (stats.profile && typeof stats.profile === "object") {
          latest = stats.profile;
        } else if (stats.stats && typeof stats.stats === "object") {
          latest = stats.stats;
        } else {
          latest = stats;
        }

        const followers = Number(latest.followers_count ?? latest.follower_count_30d ?? latest.follower_count_7d ?? latest.follower_count ?? latest.followers ?? latest.total_followers ?? 
                          stats.followers_count ?? stats.follower_count ?? stats.followers ?? 0) || 0;
        const reach = Number(latest.reach_30d ?? latest.reach_7d ?? latest.reach_1d ?? latest.reach ?? latest.total_reach ??
                      stats.reach_30d ?? stats.reach_7d ?? stats.reach ?? 0) || 0;
        const engaged = Number(latest.accounts_engaged_30d ?? latest.accounts_engaged_7d ?? latest.total_interactions_30d ?? latest.accounts_engaged ?? latest.engaged_accounts ?? latest.engagement ?? latest.engagement_count ??
                        stats.accounts_engaged_30d ?? stats.accounts_engaged ?? stats.engagement ?? 0) || 0;
        const clicks = Number(latest.website_clicks_30d ?? latest.website_clicks_7d ?? latest.website_clicks_1d ?? latest.website_clicks ?? latest.clicks ?? latest.link_clicks ??
                       stats.website_clicks_30d ?? stats.website_clicks ?? stats.clicks ?? 0) || 0;

        dashFollowers.innerText = followers.toLocaleString();
        dashReach.innerText = reach.toLocaleString();
        dashEngaged.innerText = engaged.toLocaleString();
        dashClicks.innerText = clicks.toLocaleString();

        // Parse and render the timeseries chart
        let chartData = [];
        if (stats.data && Array.isArray(stats.data.records)) {
          chartData = stats.data.records;
        } else if (Array.isArray(stats)) {
          chartData = stats;
        } else if (stats.timeseries && Array.isArray(stats.timeseries)) {
          chartData = stats.timeseries;
        } else if (stats.data && Array.isArray(stats.data)) {
          chartData = stats.data;
        }

        if (chartData.length > 0) {
          dashboardUi.renderInsightsChart("insightsChart", chartData);
        } else {
          // Render a simple flat timeline of whatever metadata is available
          dashboardUi.renderInsightsChart("insightsChart", [
            { 
              date: "Live Snapshot", 
              reach_1d: latest.reach_1d ?? latest.reach ?? latest.reach_30d ?? stats.reach ?? 0, 
              profile_views_1d: latest.profile_views_1d ?? latest.profile_views ?? latest.profile_views_30d ?? stats.profile_views ?? 0, 
              follower_count_1d: latest.followers_count ?? latest.follower_count ?? latest.follower_count_30d ?? stats.followers_count ?? 0 
            }
          ]);
        }
      }
    }
  } catch (error) {
    dashboardUi.showToast(`Failed to load profile stats: ${error.message}`, "error");
  }
}

// Helper to extract post-specific stats from different response structures
function extractPostStats(statsRes, postId) {
  if (!statsRes) return null;

  // 1. Check for nested platform records format: {"data": {"post_id": {"platforms": [{"records": [{"stats": ...}]}]}}}
  const rootData = statsRes.data || statsRes;
  if (rootData && typeof rootData === "object" && rootData[postId]) {
    const postData = rootData[postId];
    if (postData.platforms && Array.isArray(postData.platforms)) {
      const platformObj = postData.platforms.find(p => p.platform === "instagram") || postData.platforms[0];
      if (platformObj && platformObj.records && Array.isArray(platformObj.records) && platformObj.records.length > 0) {
        const latestRecord = platformObj.records[platformObj.records.length - 1];
        return latestRecord.stats || latestRecord;
      }
    }
  }

  // 2. Fallbacks
  if (Array.isArray(statsRes)) {
    const found = statsRes.find(s => s.id === postId || s.post_id === postId || s.post_ids === postId);
    if (found) return found.stats || found;
    return statsRes[0]?.stats || statsRes[0];
  }
  if (typeof statsRes === "object") {
    if (statsRes[postId]) {
      return statsRes[postId].stats || statsRes[postId];
    }
    if (statsRes.data && Array.isArray(statsRes.data)) {
      const found = statsRes.data.find(s => s.id === postId || s.post_id === postId);
      if (found) return found.stats || found;
    }
    if (statsRes.stats) return statsRes.stats;
    return statsRes;
  }
  return null;
}

/**
 * Fetch Post History & Performance
 */
async function fetchPosts() {
  const container = document.getElementById("posts-analytics-container");
  dashboardUi.showSpinner(container);

  try {
    if (state.mode === "demo") {
      const posts = await mockDb.getPosts();
      state.posts = posts;
      dashboardUi.renderPostAnalytics(container, posts);
    } else {
      // Live Mode
      // Because there is no general "list posts" endpoint in Haloocom, 
      // we merge posts published via the dashboard in this session (stored in localStorage) 
      // and posts added manually by ID.
      const sessionPosts = getLocalPublishedPosts();
      
      const postsWithStats = [];
      for (const p of sessionPosts) {
        try {
          const statsRes = await haloocomApi.getPostStats(state.apiKey, p.id);
          const parsedStats = extractPostStats(statsRes, p.id);
          p.stats = parsedStats || p.stats;
        } catch (e) {
          console.warn("Could not fetch stats for post", p.id, e);
        }
        postsWithStats.push(p);
      }
      
      state.posts = postsWithStats;
      dashboardUi.renderPostAnalytics(container, postsWithStats);
    }
  } catch (error) {
    dashboardUi.showToast(`Failed to load posts: ${error.message}`, "error");
  }
}

/**
 * Fetch Direct Message Chats
 */
async function fetchChats() {
  const container = document.getElementById("chat-list-container");
  const pane = document.getElementById("chat-pane-view");
  dashboardUi.showSpinner(container);

  try {
    let chatsList = [];
    if (state.mode === "demo") {
      chatsList = await mockDb.getChats();
    } else {
      chatsList = await haloocomApi.getChats(state.apiKey, state.profileId);
    }
    
    chatsList = normalizeArray(chatsList, "chats").map(chat => ({
      ...chat,
      chat_id: chat.id || chat.chat_id || ""
    }));

    // Concurrently fetch latest message for each chat to populate sidebar preview
    if (state.mode === "live") {
      await Promise.all(chatsList.map(async chat => {
        try {
          const msgs = await haloocomApi.getMessages(state.apiKey, chat.chat_id);
          const normalizedMsgs = normalizeArray(msgs, "messages").sort((a, b) => {
            const timeA = new Date(a.timestamp || a.created_at || a.sent_at || a.time || 0).getTime();
            const timeB = new Date(b.timestamp || b.created_at || b.sent_at || b.time || 0).getTime();
            return timeA - timeB;
          });
          if (normalizedMsgs.length > 0) {
            const latestMsg = normalizedMsgs[normalizedMsgs.length - 1];
            chat.last_message = latestMsg.body || (latestMsg.attachment ? "(Attachment)" : "(Media Attachment)");
            chat.last_message_time = latestMsg.timestamp || latestMsg.created_at || latestMsg.sent_at || latestMsg.time;
          }
        } catch (e) {
          console.warn("Could not fetch messages for chat preview:", chat.chat_id, e);
        }
      }));
    }

    state.chats = chatsList;
    
    // Choose active chat if none is set
    if (!state.activeChatId && chatsList.length > 0) {
      state.activeChatId = chatsList[0].chat_id;
    }

    dashboardUi.renderChatsList(container, chatsList, state.activeChatId, selectChat);

    if (state.activeChatId) {
      pane.classList.remove("hidden");
      fetchMessages(state.activeChatId);
    } else {
      // Hide chat container or show empty state
      pane.classList.add("hidden");
    }
  } catch (error) {
    dashboardUi.showToast(`Failed to load inbox: ${error.message}`, "error");
  }
}

/**
 * Select a active chat thread
 */
function selectChat(chatId) {
  state.activeChatId = chatId;
  
  // Re-render chat list to update highlighted active state
  const container = document.getElementById("chat-list-container");
  dashboardUi.renderChatsList(container, state.chats, state.activeChatId, selectChat);

  // Find chat details to update header
  const chat = state.chats.find(c => c.chat_id === chatId);
  if (chat) {
    document.getElementById("chat-active-username").innerText = `@${chat.participant_username || 'anonymous'}`;
    document.getElementById("chat-active-avatar").innerText = chat.participant_username ? chat.participant_username.slice(0, 2) : "UN";
    document.getElementById("chat-active-meta").innerText = `External ID: ${chat.participant_external_id || 'N/A'}`;
  }

  fetchMessages(chatId);
}

/**
 * Fetch Message Thread History
 */
async function fetchMessages(chatId) {
  const container = document.getElementById("chat-messages-container");
  dashboardUi.showSpinner(container);

  try {
    let messages = [];
    if (state.mode === "demo") {
      messages = await mockDb.getMessages(chatId);
    } else {
      messages = await haloocomApi.getMessages(state.apiKey, chatId);
    }
    
    messages = normalizeArray(messages, "messages");

    // Sort messages chronologically (oldest first, newest at bottom of chat feed)
    messages.sort((a, b) => {
      const timeA = new Date(a.timestamp || a.created_at || a.sent_at || a.time || 0).getTime();
      const timeB = new Date(b.timestamp || b.created_at || b.sent_at || b.time || 0).getTime();
      return timeA - timeB;
    });

    dashboardUi.renderConversation(container, messages, state.profileId, handleReactMessage);
  } catch (error) {
    dashboardUi.showToast(`Failed to load messages: ${error.message}`, "error");
  }
}

/**
 * Handle Send Direct Message
 */
async function handleSendDM() {
  const input = document.getElementById("chat-text-input");
  const body = input.value.trim();
  const attachmentUrl = state.pendingAttachmentUrl;

  if (!body && !attachmentUrl) return;

  const chatId = state.activeChatId;
  if (!chatId) return;

  try {
    if (state.mode === "demo") {
      const mediaArr = attachmentUrl ? [attachmentUrl] : null;
      await mockDb.sendMessage(chatId, body, mediaArr);
    } else {
      const mediaArr = attachmentUrl ? [attachmentUrl] : null;
      await haloocomApi.sendMessage(state.apiKey, chatId, body, mediaArr);
    }

    // Reset attachments state
    state.pendingAttachmentUrl = null;
    document.getElementById("btn-chat-attachment").classList.remove("active");
    input.value = "";
    
    dashboardUi.showToast("Message sent!", "success");
    fetchMessages(chatId);
    
    // Refresh chat items
    const chat = state.chats.find(c => c.chat_id === chatId);
    if (chat) {
      chat.last_message = body || "(Attachment)";
      chat.last_message_time = new Date().toISOString();
      const container = document.getElementById("chat-list-container");
      dashboardUi.renderChatsList(container, state.chats, state.activeChatId, selectChat);
    }
  } catch (error) {
    dashboardUi.showToast(`Could not send message: ${error.message}`, "error");
  }
}

/**
 * React to message (e.g. Love)
 */
async function handleReactMessage(messageId, reaction) {
  try {
    if (state.mode === "demo") {
      await mockDb.reactToMessage(messageId, reaction);
    } else {
      await haloocomApi.reactToMessage(state.apiKey, messageId, reaction);
    }
    dashboardUi.showToast("Message reaction toggled", "success");
    fetchMessages(state.activeChatId);
  } catch (error) {
    dashboardUi.showToast(`Failed to react: ${error.message}`, "error");
  }
}

/**
 * Populate Comments View Sidebar
 */
async function loadCommentsSidebar() {
  const container = document.getElementById("comments-posts-list-container");
  container.innerHTML = "";

  try {
    let postsList = [];
    if (state.mode === "demo") {
      postsList = await mockDb.getPosts();
    } else {
      // Live Mode
      // Load standard published posts + custom loaded post IDs
      const sessionPosts = getLocalPublishedPosts();
      
      const customLoaded = state.customPostIds.map(id => ({
        id: id,
        format: "post",
        body: `Post ID: ${id}`,
        media: [],
        created_at: new Date().toISOString()
      }));

      // Merge avoiding duplicates
      const merged = [...sessionPosts];
      customLoaded.forEach(item => {
        if (!merged.find(m => m.id === item.id)) {
          merged.push(item);
        }
      });
      postsList = merged;
    }

    if (!state.activePostId && postsList.length > 0) {
      state.activePostId = postsList[0].id;
    }

    dashboardUi.renderCommentPostsList(container, postsList, state.activePostId, selectCommentsPost);

    if (state.activePostId) {
      fetchComments(state.activePostId);
    } else {
      dashboardUi.showEmptyState(document.getElementById("comments-stream-container"), "fa-regular fa-comments", "No posts available. Enter a Post ID manually on the sidebar to load comments.");
    }
  } catch (error) {
    dashboardUi.showToast(`Failed to load comments sidebar: ${error.message}`, "error");
  }
}

function selectCommentsPost(postId) {
  state.activePostId = postId;
  
  // Re-render sidebar to highlight active
  loadCommentsSidebar();
}

/**
 * Fetch Comments for Post
 */
async function fetchComments(postId) {
  const container = document.getElementById("comments-stream-container");
  dashboardUi.showSpinner(container);

  try {
    let commentList = [];
    if (state.mode === "demo") {
      commentList = await mockDb.getComments(postId);
    } else {
      commentList = await haloocomApi.getComments(state.apiKey, postId, state.profileId);
    }
    
    commentList = normalizeArray(commentList, "comments");

    state.comments = commentList;
    dashboardUi.renderCommentsStream(container, commentList, handleCommentAction);
  } catch (error) {
    dashboardUi.showToast(`Failed to load comments: ${error.message}`, "error");
  }
}

/**
 * Handle Comment Moderation Actions
 */
function handleCommentAction(action, commentId, commenterUsername) {
  const postId = state.activePostId;
  if (!postId) return;

  if (action === "reply") {
    // Open reply drawer overlay
    state.replyingToCommentId = commentId;
    state.replyingToUsername = commenterUsername;
    document.getElementById("reply-target-username").innerText = `@${commenterUsername}`;
    document.getElementById("reply-composer-drawer").style.display = "block";
    document.getElementById("comment-reply-textarea").focus();
  } else if (action === "toggle-hide") {
    handleToggleHideComment(postId, commentId);
  } else if (action === "delete") {
    handleDeleteComment(postId, commentId);
  } else if (action === "private-dm") {
    // Open reply drawer in private DM mode
    state.replyingToCommentId = commentId;
    state.replyingToUsername = commenterUsername;
    document.getElementById("reply-target-username").innerText = `@${commenterUsername} (via DM)`;
    document.getElementById("reply-composer-drawer").style.display = "block";
    document.getElementById("comment-reply-textarea").focus();
  }
}

function closeReplyDrawer() {
  document.getElementById("reply-composer-drawer").style.display = "none";
  document.getElementById("comment-reply-textarea").value = "";
  state.replyingToCommentId = null;
  state.replyingToUsername = null;
}

/**
 * Submit Comment Reply (Public comment or Private DM)
 */
async function submitCommentReply(isPrivate = false) {
  const textarea = document.getElementById("comment-reply-textarea");
  const body = textarea.value.trim();

  if (!body) {
    dashboardUi.showToast("Reply content cannot be empty.", "error");
    return;
  }

  const postId = state.activePostId;
  const parentCommentId = state.replyingToCommentId;
  
  if (!postId || !parentCommentId) return;

  try {
    if (isPrivate) {
      // Send private message (re-routing commenter as a DM recipient)
      if (state.mode === "demo") {
        await mockDb.privateReply(postId, parentCommentId, body);
      } else {
        await haloocomApi.privateReply(state.apiKey, postId, parentCommentId, state.profileId, body);
      }
      dashboardUi.showToast(`Private DM reply sent successfully to @${state.replyingToUsername}!`, "success");
    } else {
      // Public comment reply
      if (state.mode === "demo") {
        await mockDb.addCommentReply(postId, body, parentCommentId);
      } else {
        await haloocomApi.addCommentReply(state.apiKey, postId, body, state.profileId, parentCommentId);
      }
      dashboardUi.showToast("Comment reply published publicly!", "success");
    }

    closeReplyDrawer();
    fetchComments(postId);
  } catch (error) {
    dashboardUi.showToast(`Reply failed: ${error.message}`, "error");
  }
}

/**
 * Hide / Unhide comment
 */
async function handleToggleHideComment(postId, commentId) {
  try {
    let comment = state.comments.find(c => c.id === commentId);
    if (!comment) return;

    if (state.mode === "demo") {
      await mockDb.toggleHideComment(postId, commentId);
    } else {
      if (comment.hidden) {
        await haloocomApi.unhideComment(state.apiKey, postId, commentId, state.profileId);
      } else {
        await haloocomApi.hideComment(state.apiKey, postId, commentId, state.profileId);
      }
    }

    dashboardUi.showToast(`Comment state updated!`, "success");
    fetchComments(postId);
  } catch (error) {
    dashboardUi.showToast(`Hide action failed: ${error.message}`, "error");
  }
}

/**
 * Delete Comment
 */
async function handleDeleteComment(postId, commentId) {
  if (!confirm("Are you sure you want to permanently delete this comment from Instagram?")) return;

  try {
    if (state.mode === "demo") {
      await mockDb.deleteComment(postId, commentId);
    } else {
      await haloocomApi.deleteComment(state.apiKey, postId, commentId, state.profileId);
    }
    dashboardUi.showToast("Comment deleted successfully!", "success");
    fetchComments(postId);
  } catch (error) {
    dashboardUi.showToast(`Deletion failed: ${error.message}`, "error");
  }
}

/**
 * Handle Publish content (Feed, Reel, Story)
 */
async function handlePublishPost() {
  const submitBtn = document.getElementById("btn-pub-submit");
  const format = document.querySelector('input[name="pub-format"]:checked').value;
  const mediaVal = document.getElementById("pub-media-url").value.trim();
  const captionVal = document.getElementById("pub-caption").value.trim();
  
  if (!mediaVal) {
    dashboardUi.showToast("Media URL attachment is required for all formats.", "error");
    return;
  }

  const mediaList = mediaVal.split(",").map(u => u.trim()).filter(Boolean);

  const payload = {
    format: format,
    media: mediaList,
    body: captionVal,
    firstComment: document.getElementById("pub-first-comment").value.trim(),
    collaborators: document.getElementById("pub-collaborators").value.trim(),
    coverUrl: document.getElementById("pub-cover-url").value.trim(),
    audioName: document.getElementById("pub-audio-name").value.trim(),
    trialStrategy: document.getElementById("pub-trial-strategy").value,
    thumbOffset: ""
  };

  const originalHtml = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Processing & Uploading...`;

  try {
    let result = null;
    if (state.mode === "demo") {
      // Mock publish
      result = await mockDb.createPost({
        post: { body: payload.body },
        media: payload.media,
        platforms: {
          instagram: {
            format: payload.format,
            first_comment: payload.firstComment,
            collaborators: payload.collaborators ? payload.collaborators.split(",") : null,
            cover_url: payload.coverUrl,
            audio_name: payload.audioName
          }
        }
      });
    } else {
      // Live publish
      result = await haloocomApi.createPost(state.apiKey, state.profileId, payload);
    }

    if (result && (result.success || result.post_id || result.id)) {
      dashboardUi.showToast("Instagram post created and queued for publication!", "success");
      
      // If Live mode, write to local published posts cache to display in stats dashboard
      if (state.mode === "live") {
        const publishedPost = {
          id: result.post_id || result.id || "post_" + Math.random().toString(36).substr(2, 9),
          format: payload.format,
          body: payload.body,
          media: payload.media,
          created_at: new Date().toISOString(),
          stats: { impressions: 0, likes: 0, comments: 0 }
        };
        saveLocalPublishedPost(publishedPost);
      }

      // Reset form
      document.getElementById("publisher-form").reset();
      document.querySelectorAll(".conditional-fields").forEach(el => {
        if (el.id !== "group-pub-first-comment" && el.id !== "group-pub-collaborators" && el.id !== "group-pub-caption") {
          el.style.display = "none";
        }
      });
      triggerLivePreview();

      // Redirect user to dashboard to see performance card
      document.querySelector('[data-target="dashboard"]').click();
    }
  } catch (error) {
    dashboardUi.showToast(`Publishing failed: ${error.message}`, "error");
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;
  }
}

/**
 * Cache published post metadata in localStorage for local dashboard analytics mapping in Live Mode
 */
function getLocalPublishedPosts() {
  try {
    return JSON.parse(localStorage.getItem(KEYS.PUBLISHED_POSTS)) || [];
  } catch (e) {
    return [];
  }
}

function saveLocalPublishedPost(post) {
  const posts = getLocalPublishedPosts();
  posts.unshift(post); // Insert at top
  localStorage.setItem(KEYS.PUBLISHED_POSTS, JSON.stringify(posts));
}

/**
 * Real-time Polling & Notification Manager
 */
let pollingInterval = null;
function startBackgroundPolling() {
  if (pollingInterval) clearInterval(pollingInterval);
  // Poll every 12 seconds
  pollingInterval = setInterval(() => {
    if (state.apiKey && state.profileId) {
      pollForUpdates();
    }
  }, 12000);
}

async function pollForUpdates() {
  let playSound = false;

  // 1. Poll Chats & Messages
  try {
    const rawChats = await haloocomApi.getChats(state.apiKey, state.profileId);
    const chatsList = normalizeArray(rawChats, "chats").map(chat => ({
      ...chat,
      chat_id: chat.id || chat.chat_id || ""
    }));

    let newMsgsCount = 0;
    for (const chat of chatsList) {
      const lastTime = chat.last_message_at || 
                       chat.last_inbound_at || 
                       chat.last_outbound_at || 
                       chat.last_message_time || 
                       chat.updated_at || 
                       (chat.last_message && typeof chat.last_message === "object" && (chat.last_message.timestamp || chat.last_message.created_at || chat.last_message.sent_at)) || 
                       chat.timestamp || 
                       chat.created_at;

      if (lastTime) {
        const lastSeen = state.seenChatTimes[chat.chat_id];
        if (lastSeen && new Date(lastTime).getTime() > new Date(lastSeen).getTime()) {
          try {
            // Fetch messages to get the actual text and check if inbound
            const msgs = await haloocomApi.getMessages(state.apiKey, chat.chat_id);
            const normalizedMsgs = normalizeArray(msgs, "messages").sort((a, b) => {
              const timeA = new Date(a.timestamp || a.created_at || a.sent_at || a.time || 0).getTime();
              const timeB = new Date(b.timestamp || b.created_at || b.sent_at || b.time || 0).getTime();
              return timeA - timeB;
            });
            if (normalizedMsgs.length > 0) {
              const latestMsg = normalizedMsgs[normalizedMsgs.length - 1];
              const isOutbound = latestMsg.sender === "outbound" || 
                                 latestMsg.sender === "me" ||
                                 latestMsg.direction === "outbound" || 
                                 latestMsg.is_outbound === true || 
                                 latestMsg.outbound === true ||
                                 latestMsg.from_me === true ||
                                 (state.profileId && latestMsg.sender_id === state.profileId) ||
                                 (state.profileId && latestMsg.sender === state.profileId);
              
              const lastMsgText = latestMsg.body || (latestMsg.attachment ? "(Attachment)" : "(Media Attachment)");
              
              chat.last_message = lastMsgText;
              chat.last_message_time = lastTime;

              if (!isOutbound) {
                // If not currently reading this chat thread, increment unread count
                if (!(chat.chat_id === state.activeChatId && state.activeTab === "messages")) {
                  newMsgsCount++;
                }
                
                addNotification("message", `New DM from @${chat.participant_username || 'user'}`, lastMsgText);
                playSound = true;
              }
              
              // Refresh active chat if we are reading it
              if (chat.chat_id === state.activeChatId && state.activeTab === "messages") {
                const msgContainer = document.getElementById("chat-messages-container");
                if (msgContainer) {
                  dashboardUi.renderConversation(msgContainer, normalizedMsgs, state.profileId, handleReactMessage);
                }
              }
            }
          } catch (msgErr) {
            console.error("Failed to fetch messages for notification check:", msgErr);
          }
          
          // Mark as seen so we don't alert again
          state.seenChatTimes[chat.chat_id] = lastTime;
        } else if (!lastSeen) {
          state.seenChatTimes[chat.chat_id] = lastTime;
          // Hydrate the first message content if missing
          if (!chat.last_message) {
            try {
              const msgs = await haloocomApi.getMessages(state.apiKey, chat.chat_id);
              const normalizedMsgs = normalizeArray(msgs, "messages").sort((a, b) => {
                const timeA = new Date(a.timestamp || a.created_at || a.sent_at || a.time || 0).getTime();
                const timeB = new Date(b.timestamp || b.created_at || b.sent_at || b.time || 0).getTime();
                return timeA - timeB;
              });
              if (normalizedMsgs.length > 0) {
                const latestMsg = normalizedMsgs[normalizedMsgs.length - 1];
                chat.last_message = latestMsg.body || (latestMsg.attachment ? "(Attachment)" : "(Media Attachment)");
                chat.last_message_time = latestMsg.timestamp || latestMsg.created_at || latestMsg.sent_at || latestMsg.time;
              }
            } catch (e) {
              console.warn("Could not fetch messages for chat preview on initial poll:", e);
            }
          }
        }
      }
    }

    state.chats = chatsList;

    if (state.activeTab === "messages") {
      const container = document.getElementById("chat-list-container");
      if (container) {
        dashboardUi.renderChatsList(container, chatsList, state.activeChatId, selectChat);
      }
    }

    if (newMsgsCount > 0) {
      state.unreadMessages += newMsgsCount;
      updateSidebarBadges();
    }
  } catch (e) {
    console.error("Polling chats failed:", e);
  }

  // 2. Poll Comments for all registered posts
  const postIdsToPoll = new Set();
  if (state.posts && Array.isArray(state.posts)) {
    state.posts.forEach(p => { if (p.id) postIdsToPoll.add(p.id); });
  }
  if (state.customPostIds && Array.isArray(state.customPostIds)) {
    state.customPostIds.forEach(id => postIdsToPoll.add(id));
  }
  if (state.activePostId) {
    postIdsToPoll.add(state.activePostId);
  }

  let newCommentsCount = 0;
  for (const postId of postIdsToPoll) {
    try {
      const rawComments = await haloocomApi.getComments(state.apiKey, postId, state.profileId);
      const commentList = normalizeArray(rawComments, "comments");
      
      const isFirstPollForPost = !state.polledPostIds.has(postId);

      for (const comment of commentList) {
        if (comment.id && !state.seenCommentIds.has(comment.id)) {
          if (!isFirstPollForPost) {
            newCommentsCount++;
            const author = comment.username || comment.author_username || "user";
            addNotification("comment", `New Comment on Post`, `@${author}: "${comment.body}"`);
            playSound = true;
          }
          state.seenCommentIds.add(comment.id);
        }

        if (comment.replies && Array.isArray(comment.replies)) {
          for (const reply of comment.replies) {
            if (reply.id && !state.seenCommentIds.has(reply.id)) {
              if (!isFirstPollForPost) {
                newCommentsCount++;
                const author = reply.username || reply.author_username || "user";
                addNotification("comment", `New Reply on Comment`, `@${author}: "${reply.body}"`);
                playSound = true;
              }
              state.seenCommentIds.add(reply.id);
            }
          }
        }
      }

      // Mark post as polled so subsequent polls will trigger notifications
      state.polledPostIds.add(postId);

      // If this post is the active one and we are on comments tab, update UI
      if (postId === state.activePostId && state.activeTab === "comments") {
        const container = document.getElementById("comments-stream-container");
        if (container) {
          dashboardUi.renderCommentsStream(container, commentList, handleCommentAction);
        }
      }
    } catch (commentErr) {
      console.error(`Polling comments failed for post ${postId}:`, commentErr);
    }
  }

  if (newCommentsCount > 0) {
    state.unreadComments += newCommentsCount;
    updateSidebarBadges();
  }

  // 3. Poll Profile Stats (Followers count)
  try {
    const stats = await haloocomApi.getProfileStats(state.apiKey, state.profileId);
    if (stats) {
      let latest = stats.data || stats;
      if (stats.data && Array.isArray(stats.data.records) && stats.data.records.length > 0) {
        const records = stats.data.records;
        latest = records[records.length - 1].stats || records[records.length - 1];
      }
      
      const followers = Number(latest.followers_count ?? latest.follower_count ?? 0) || 0;
      if (state.previousFollowers !== null && followers > state.previousFollowers) {
        const diff = followers - state.previousFollowers;
        addNotification("stat", "Follower Growth Alert", `Congratulations! You gained ${diff} new follower${diff > 1 ? 's' : ''}!`);
        playSound = true;
        
        if (state.activeTab === "dashboard") {
          refreshViewData("dashboard");
        }
      }
      state.previousFollowers = followers;
    }
  } catch (e) {
    console.error("Polling stats failed:", e);
  }

  if (playSound) {
    playNotificationSound();
  }
}

function addNotification(type, title, message) {
  const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  const notif = {
    id: "notif_" + Math.random().toString(36).substr(2, 9),
    type,
    title,
    message,
    time,
    read: false
  };
  state.notifications.push(notif);
  if (state.notifications.length > 50) {
    state.notifications.shift();
  }

  if (state.activeTab !== "notifications") {
    updateSidebarBadges();
  } else {
    const container = document.getElementById("notifications-list-container");
    if (container) {
      dashboardUi.renderNotificationsList(container, state.notifications);
    }
  }
}

function updateSidebarBadges() {
  const msgBadge = document.getElementById("nav-badge-messages");
  const commentBadge = document.getElementById("nav-badge-comments");
  const notifBadge = document.getElementById("nav-badge-notifications");

  if (msgBadge) {
    if (state.unreadMessages > 0) {
      msgBadge.innerText = state.unreadMessages;
      msgBadge.classList.remove("hidden");
    } else {
      msgBadge.classList.add("hidden");
    }
  }

  if (commentBadge) {
    if (state.unreadComments > 0) {
      commentBadge.innerText = state.unreadComments;
      commentBadge.classList.remove("hidden");
    } else {
      commentBadge.classList.add("hidden");
    }
  }

  const unreadNotifs = state.notifications.filter(n => !n.read).length;
  if (notifBadge) {
    if (unreadNotifs > 0) {
      notifBadge.innerText = unreadNotifs;
      notifBadge.classList.remove("hidden");
    } else {
      notifBadge.classList.add("hidden");
    }
  }
}

function clearMessagesBadge() {
  state.unreadMessages = 0;
  updateSidebarBadges();
}

function clearCommentsBadge() {
  state.unreadComments = 0;
  updateSidebarBadges();
}

function clearNotificationsBadge() {
  state.notifications.forEach(n => n.read = true);
  updateSidebarBadges();
}

function playNotificationSound() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    
    osc.type = "sine";
    osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
    osc.frequency.setValueAtTime(880, ctx.currentTime + 0.12); // A5
    
    gain.gain.setValueAtTime(0.15, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.45);
    
    osc.connect(gain);
    gain.connect(ctx.destination);
    
    osc.start();
    osc.stop(ctx.currentTime + 0.45);
  } catch (e) {
    console.warn("Audio Context blocked or unsupported:", e);
  }
}

function initNotificationsManager() {
  const btnClear = document.getElementById("btn-clear-notifications");
  if (btnClear) {
    btnClear.addEventListener("click", () => {
      state.notifications = [];
      updateSidebarBadges();
      const container = document.getElementById("notifications-list-container");
      if (container) {
        dashboardUi.renderNotificationsList(container, state.notifications);
      }
      dashboardUi.showToast("Notifications cleared", "info");
    });
  }
}
