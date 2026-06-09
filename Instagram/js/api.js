/**
 * Haloocom Live API Client
 * Wraps REST endpoints for Instagram publishing, direct messages, comments, and stats.
 */

const BASE_URL = (window.location.origin && !window.location.origin.startsWith("file://"))
  ? window.location.origin
  : "https://api.postproxy.dev";

// Helper to construct headers
function getHeaders(apiKey) {
  return {
    "Authorization": `Bearer ${apiKey}`,
    "Content-Type": "application/json"
  };
}

// Helper for fetch requests with standardized error handling
// Global API logs store
window.apiLogs = window.apiLogs || [];
function logTransaction(method, url, status, responseText) {
  window.apiLogs.push({
    timestamp: new Date().toLocaleTimeString(),
    method,
    url,
    status,
    response: responseText
  });
  if (window.apiLogs.length > 30) {
    window.apiLogs.shift();
  }
  if (window.updateApiLogsUi) {
    window.updateApiLogsUi();
  }
}

// Helper for fetch requests with standardized error handling
async function request(url, options) {
  const method = options?.method || "GET";
  try {
    const response = await fetch(url, options);
    let data = null;
    let rawText = "";
    
    const contentType = response.headers.get("content-type");
    if (contentType && contentType.includes("application/json")) {
      const cloned = response.clone();
      try {
        data = await response.json();
        rawText = JSON.stringify(data);
      } catch (e) {
        rawText = await cloned.text();
        data = { text: rawText };
      }
    } else {
      rawText = await response.text();
      data = { text: rawText };
    }

    logTransaction(method, url, response.status, rawText);

    if (!response.ok) {
      const errorMsg = data?.error || data?.message || `HTTP error! Status: ${response.status}`;
      throw new Error(errorMsg);
    }
    return data;
  } catch (error) {
    console.error(`Haloocom API error at [${method}] ${url}:`, error);
    logTransaction(method, url, "ERROR", error.message);
    throw error;
  }
}

export const haloocomApi = {
  /**
   * Test Connection
   * Hits the profile stats endpoint as a lightweight credential check.
   */
  async testConnection(apiKey, profileId) {
    const url = `${BASE_URL}/api/profiles/${profileId}/stats`;
    const data = await request(url, {
      method: "GET",
      headers: getHeaders(apiKey)
    });
    // If it returns successfully (even an empty timeseries), we are authenticated.
    return { status: "connected", raw: data };
  },

  /**
   * Fetch Profile Stats Timeseries
   */
  async getProfileStats(apiKey, profileId) {
    const url = `${BASE_URL}/api/profiles/${profileId}/stats`;
    return await request(url, {
      method: "GET",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Fetch Post Stats
   */
  async getPostStats(apiKey, postId) {
    const url = `${BASE_URL}/api/posts/stats?post_ids=${postId}&profiles=instagram`;
    return await request(url, {
      method: "GET",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * List Chats for Profile
   */
  async getChats(apiKey, profileId) {
    const url = `${BASE_URL}/api/profiles/${profileId}/chats`;
    return await request(url, {
      method: "GET",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Get messages inside a chat (assuming standard endpoint structure)
   */
  async getMessages(apiKey, chatId) {
    const url = `${BASE_URL}/api/chats/${chatId}/messages`;
    return await request(url, {
      method: "GET",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Create or Find Chat
   */
  async createChat(apiKey, profileId, participantExternalId, participantUsername) {
    const url = `${BASE_URL}/api/profiles/${profileId}/chats`;
    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey),
      body: JSON.stringify({
        participant_external_id: participantExternalId,
        participant_username: participantUsername
      })
    });
  },

  /**
   * Send DM Message (accepts text or single media attachment)
   */
  async sendMessage(apiKey, chatId, body, media = null) {
    const url = `${BASE_URL}/api/chats/${chatId}/messages`;
    const payload = {};
    if (media && media.length > 0) {
      payload.media = [media[0]]; // accepts one attachment per send
      if (body) payload.body = body; // Haloocom supports passing text alongside media sometimes or separately
    } else {
      payload.body = body || "";
    }

    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey),
      body: JSON.stringify(payload)
    });
  },

  /**
   * React to Message
   */
  async reactToMessage(apiKey, messageId, reaction = "love") {
    const url = `${BASE_URL}/api/messages/${messageId}/react`;
    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey),
      body: JSON.stringify({ reaction })
    });
  },

  /**
   * List Comments on a Post
   */
  async getComments(apiKey, postId, profileId) {
    const url = `${BASE_URL}/api/posts/${postId}/comments?profile_id=${profileId}`;
    return await request(url, {
      method: "GET",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Reply to Comment (or post if parentId is null)
   */
  async addCommentReply(apiKey, postId, body, profileId, parentId = null) {
    const url = `${BASE_URL}/api/posts/${postId}/comments?profile_id=${profileId}`;
    const payload = { body };
    if (parentId) {
      payload.parent_id = parentId;
    }
    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey),
      body: JSON.stringify(payload)
    });
  },

  /**
   * Hide Comment
   */
  async hideComment(apiKey, postId, commentId, profileId) {
    const url = `${BASE_URL}/api/posts/${postId}/comments/${commentId}/hide?profile_id=${profileId}`;
    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Unhide Comment
   */
  async unhideComment(apiKey, postId, commentId, profileId) {
    const url = `${BASE_URL}/api/posts/${postId}/comments/${commentId}/unhide?profile_id=${profileId}`;
    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Delete Comment
   */
  async deleteComment(apiKey, postId, commentId, profileId) {
    const url = `${BASE_URL}/api/posts/${postId}/comments/${commentId}?profile_id=${profileId}`;
    return await request(url, {
      method: "DELETE",
      headers: getHeaders(apiKey)
    });
  },

  /**
   * Private Reply to Commenter
   */
  async privateReply(apiKey, postId, commentId, profileId, text) {
    const url = `${BASE_URL}/api/posts/${postId}/comments/${commentId}/private_reply?profile_id=${profileId}`;
    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey),
      body: JSON.stringify({ text })
    });
  },

  /**
   * Create / Publish Post
   */
  async createPost(apiKey, profileId, postData) {
    const url = `${BASE_URL}/api/posts`;
    // Standardize post data format
    const payload = {
      post: { body: postData.body || "" },
      profiles: [profileId],
      media: postData.media || [],
      platforms: {
        instagram: {
          format: postData.format || "post"
        }
      }
    };

    // Format specific attributes
    const ins = payload.platforms.instagram;
    if (postData.format === "post") {
      if (postData.firstComment) ins.first_comment = postData.firstComment;
      if (postData.collaborators) ins.collaborators = postData.collaborators.split(",").map(c => c.trim()).filter(Boolean);
    } else if (postData.format === "reel") {
      if (postData.firstComment) ins.first_comment = postData.firstComment;
      if (postData.coverUrl) ins.cover_url = postData.coverUrl;
      if (postData.audioName) ins.audio_name = postData.audioName;
      if (postData.trialStrategy) ins.trial_strategy = postData.trialStrategy;
      if (postData.collaborators) ins.collaborators = postData.collaborators.split(",").map(c => c.trim()).filter(Boolean);
      if (postData.thumbOffset) ins.thumb_offset = postData.thumbOffset;
    } else if (postData.format === "story") {
      // Stories do not support text captions, default to empty body
      payload.post = { body: "" };
    }

    return await request(url, {
      method: "POST",
      headers: getHeaders(apiKey),
      body: JSON.stringify(payload)
    });
  }
};
