/**
 * Mock Data Engine for Instagram Haloocom Dashboard Client
 * Provides realistic mock datasets and simulate network latency & state changes
 */

export const mockProfileStats = {
  followers_count: 12480,
  follows_count: 482,
  media_count: 184,
  timeseries: [
    { date: "06-03", reach_1d: 1200, profile_views_1d: 140, total_interactions_1d: 95, website_clicks_1d: 12, follower_count_1d: 12410 },
    { date: "06-04", reach_1d: 1540, profile_views_1d: 185, total_interactions_1d: 110, website_clicks_1d: 18, follower_count_1d: 12425 },
    { date: "06-05", reach_1d: 2100, profile_views_1d: 290, total_interactions_1d: 230, website_clicks_1d: 35, follower_count_1d: 12448 },
    { date: "06-06", reach_1d: 1800, profile_views_1d: 210, total_interactions_1d: 150, website_clicks_1d: 22, follower_count_1d: 12460 },
    { date: "06-07", reach_1d: 2500, profile_views_1d: 310, total_interactions_1d: 290, website_clicks_1d: 41, follower_count_1d: 12468 },
    { date: "06-08", reach_1d: 3200, profile_views_1d: 450, total_interactions_1d: 410, website_clicks_1d: 65, follower_count_1d: 12475 },
    { date: "06-09", reach_1d: 3800, profile_views_1d: 520, total_interactions_1d: 480, website_clicks_1d: 82, follower_count_1d: 12480 }
  ],
  insights: {
    reach_30d: 42100,
    profile_views_30d: 5900,
    accounts_engaged_30d: 8400,
    total_interactions_30d: 4850,
    website_clicks_30d: 620,
    follower_count_30d: 12480
  }
};

export const mockPosts = [
  {
    id: "post_feed_001",
    format: "post",
    body: "🚀 Announcing the launch of our new AI Developer Dashboard! Simplify your Instagram automation in one click. #buildinpublic #devtool",
    media: ["https://picsum.photos/id/1/800/800", "https://picsum.photos/id/2/800/800"],
    first_comment: "Let us know your thoughts below! 👇",
    created_at: "2026-06-08T10:00:00Z",
    stats: {
      impressions: 4850,
      likes: 342,
      comments: 18,
      saved: 84,
      profile_visits: 124,
      follows: 12
    }
  },
  {
    id: "post_reel_002",
    format: "reel",
    body: "A quick tutorial on setting up webhooks with Haloocom! Super fast and reliable. 🎬",
    media: ["https://assets.mixkit.co/videos/preview/mixkit-code-running-on-a-computer-screen-close-up-30230-large.mp4"],
    cover_url: "https://picsum.photos/id/60/400/700",
    audio_name: "Trending Dev Beats",
    created_at: "2026-06-07T15:30:00Z",
    stats: {
      impressions: 12400,
      likes: 954,
      comments: 32,
      saved: 345,
      profile_visits: 480,
      follows: 48
    }
  },
  {
    id: "post_story_003",
    format: "story",
    body: "", // Stories have no caption
    media: ["https://picsum.photos/id/102/600/1000"],
    created_at: "2026-06-09T08:15:00Z",
    stats: null // Stories do not return stats
  }
];

export const mockChats = [
  {
    chat_id: "chat_001",
    participant_username: "janedoe",
    participant_external_id: "17841400000000001",
    last_message: "Thanks! That worked perfectly.",
    last_message_time: "2026-06-09T14:45:00Z",
    messages: [
      {
        id: "msg_101",
        body: "Hi! I'm having trouble connecting my professional account.",
        sender: "inbound",
        timestamp: "2026-06-09T14:30:00Z",
        reactions: []
      },
      {
        id: "msg_102",
        body: "Make sure your Instagram account is linked to a Facebook Page, and you have granted business permissions.",
        sender: "outbound",
        timestamp: "2026-06-09T14:35:00Z",
        reactions: []
      },
      {
        id: "msg_103",
        body: "Thanks! That worked perfectly.",
        sender: "inbound",
        timestamp: "2026-06-09T14:45:00Z",
        reactions: [{ user: "me", reaction: "love" }]
      }
    ]
  },
  {
    chat_id: "chat_002",
    participant_username: "alex_dev",
    participant_external_id: "17841400000000002",
    last_message: "Mentioned you in their story",
    last_message_time: "2026-06-09T10:10:00Z",
    messages: [
      {
        id: "msg_201",
        body: "Hey there, is there a rate limit on sending private replies?",
        sender: "inbound",
        timestamp: "2026-06-09T09:00:00Z",
        reactions: []
      },
      {
        id: "msg_202",
        body: "Yes, it must be sent within 7 days of the comment, and is allowed once per comment.",
        sender: "outbound",
        timestamp: "2026-06-09T09:15:00Z",
        reactions: []
      },
      {
        id: "msg_203",
        body: "",
        sender: "inbound",
        timestamp: "2026-06-09T10:10:00Z",
        attachment: {
          type: "story_mention",
          url: "https://picsum.photos/id/201/600/1000"
        },
        reactions: []
      }
    ]
  },
  {
    chat_id: "chat_003",
    participant_username: "samantha_k",
    participant_external_id: "17841400000000003",
    last_message: "Awesome feature!",
    last_message_time: "2026-06-08T18:22:00Z",
    messages: [
      {
        id: "msg_301",
        body: "Awesome feature!",
        sender: "inbound",
        timestamp: "2026-06-08T18:22:00Z",
        reactions: []
      }
    ]
  }
];

export const mockComments = {
  post_feed_001: [
    {
      id: "cmt_001",
      body: "This is huge! When will the webhook client support reaction receipts?",
      created_at: "2026-06-08T11:15:00Z",
      username: "tech_guru",
      is_verified_user: true,
      is_user_follow_business: true,
      is_business_follow_user: false,
      follower_count: 45200,
      hidden: false,
      replies: [
        {
          id: "cmt_001_r1",
          body: "They are supported right now under `reaction.received` event!",
          created_at: "2026-06-08T11:45:00Z",
          username: "your_brand",
          is_verified_user: false,
          is_user_follow_business: false,
          is_business_follow_user: false,
          follower_count: 12480
        }
      ]
    },
    {
      id: "cmt_002",
      body: "Can you mix video and images in the carousel? 🤔",
      created_at: "2026-06-08T12:30:00Z",
      username: "creative_mind",
      is_verified_user: false,
      is_user_follow_business: true,
      is_business_follow_user: true,
      follower_count: 1200,
      hidden: false,
      replies: []
    },
    {
      id: "cmt_003",
      body: "Spam message buy followers here at cheaprate!",
      created_at: "2026-06-08T14:10:00Z",
      username: "bot_spammer_99",
      is_verified_user: false,
      is_user_follow_business: false,
      is_business_follow_user: false,
      follower_count: 5,
      hidden: true,
      replies: []
    }
  ],
  post_reel_002: [
    {
      id: "cmt_201",
      body: "Love the audio track choice. Video tutorial is very clean!",
      created_at: "2026-06-07T16:00:00Z",
      username: "code_ninja",
      is_verified_user: false,
      is_user_follow_business: false,
      is_business_follow_user: false,
      follower_count: 980,
      hidden: false,
      replies: []
    }
  ],
  post_story_003: []
};

// Database state simulation wrapper
class MockDatabase {
  constructor() {
    this.profileStats = JSON.parse(JSON.stringify(mockProfileStats));
    this.posts = JSON.parse(JSON.stringify(mockPosts));
    this.chats = JSON.parse(JSON.stringify(mockChats));
    this.comments = JSON.parse(JSON.stringify(mockComments));
  }

  delay(ms = 300) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  async testConnection(apiKey, profileId) {
    await this.delay(400);
    if (!apiKey || !profileId) {
      throw new Error("Invalid parameters. API Key and Profile ID are required.");
    }
    return { status: "connected", account_type: "Business", username: "dev_sandbox" };
  }

  async getProfileStats() {
    await this.delay(200);
    return this.profileStats;
  }

  async getPosts() {
    await this.delay(200);
    return this.posts;
  }

  async getChats() {
    await this.delay(250);
    return this.chats.map(c => ({
      chat_id: c.chat_id,
      participant_username: c.participant_username,
      participant_external_id: c.participant_external_id,
      last_message: c.last_message,
      last_message_time: c.last_message_time
    }));
  }

  async getMessages(chatId) {
    await this.delay(200);
    const chat = this.chats.find(c => c.chat_id === chatId);
    if (!chat) throw new Error("Chat not found");
    return chat.messages;
  }

  async sendMessage(chatId, body, media = null) {
    await this.delay(400);
    const chat = this.chats.find(c => c.chat_id === chatId);
    if (!chat) throw new Error("Chat not found");

    const newMsg = {
      id: "msg_" + Math.random().toString(36).substr(2, 9),
      body: body || "",
      sender: "outbound",
      timestamp: new Date().toISOString(),
      reactions: []
    };

    if (media && media.length > 0) {
      newMsg.attachment = { type: "image", url: media[0] };
      newMsg.body = body || "Sent an attachment";
    }

    chat.messages.push(newMsg);
    chat.last_message = newMsg.body;
    chat.last_message_time = newMsg.timestamp;

    return { success: true, message: newMsg };
  }

  async reactToMessage(messageId, reaction) {
    await this.delay(150);
    for (let chat of this.chats) {
      const msg = chat.messages.find(m => m.id === messageId);
      if (msg) {
        const existing = msg.reactions.find(r => r.user === "me");
        if (existing) {
          if (existing.reaction === reaction) {
            // Toggle off
            msg.reactions = msg.reactions.filter(r => r.user !== "me");
          } else {
            existing.reaction = reaction;
          }
        } else {
          msg.reactions.push({ user: "me", reaction });
        }
        return { success: true, reactions: msg.reactions };
      }
    }
    throw new Error("Message not found");
  }

  async getComments(postId) {
    await this.delay(200);
    return this.comments[postId] || [];
  }

  async addCommentReply(postId, body, parentId = null) {
    await this.delay(300);
    if (!this.comments[postId]) {
      this.comments[postId] = [];
    }

    const newComment = {
      id: "cmt_" + Math.random().toString(36).substr(2, 9),
      body: body,
      created_at: new Date().toISOString(),
      username: "your_brand",
      is_verified_user: false,
      is_user_follow_business: false,
      is_business_follow_user: false,
      follower_count: this.profileStats.followers_count
    };

    if (parentId) {
      const parent = this.comments[postId].find(c => c.id === parentId);
      if (!parent) throw new Error("Parent comment not found");
      if (!parent.replies) parent.replies = [];
      parent.replies.push(newComment);
    } else {
      newComment.hidden = false;
      newComment.replies = [];
      this.comments[postId].push(newComment);
    }

    // Update count in post stats
    const post = this.posts.find(p => p.id === postId);
    if (post && post.stats) {
      post.stats.comments++;
    }

    return { success: true, comment: newComment };
  }

  async toggleHideComment(postId, commentId) {
    await this.delay(200);
    const commentsList = this.comments[postId];
    if (!commentsList) throw new Error("Post has no comments");

    const comment = commentsList.find(c => c.id === commentId);
    if (!comment) throw new Error("Comment not found");

    comment.hidden = !comment.hidden;
    return { success: true, hidden: comment.hidden };
  }

  async deleteComment(postId, commentId) {
    await this.delay(250);
    const commentsList = this.comments[postId];
    if (!commentsList) throw new Error("Post has no comments");

    const index = commentsList.findIndex(c => c.id === commentId);
    if (index !== -1) {
      commentsList.splice(index, 1);
      const post = this.posts.find(p => p.id === postId);
      if (post && post.stats) post.stats.comments = Math.max(0, post.stats.comments - 1);
      return { success: true };
    }

    // Try finding in replies
    for (let c of commentsList) {
      if (c.replies) {
        const replyIndex = c.replies.findIndex(r => r.id === commentId);
        if (replyIndex !== -1) {
          c.replies.splice(replyIndex, 1);
          const post = this.posts.find(p => p.id === postId);
          if (post && post.stats) post.stats.comments = Math.max(0, post.stats.comments - 1);
          return { success: true };
        }
      }
    }

    throw new Error("Comment not found");
  }

  async privateReply(postId, commentId, text) {
    await this.delay(400);
    // Find comment to get username
    const commentsList = this.comments[postId];
    if (!commentsList) throw new Error("Post not found");
    const comment = commentsList.find(c => c.id === commentId);
    if (!comment) throw new Error("Comment not found");

    // Check if chat already exists
    let chat = this.chats.find(c => c.participant_username === comment.username);
    if (!chat) {
      chat = {
        chat_id: "chat_" + Math.random().toString(36).substr(2, 9),
        participant_username: comment.username,
        participant_external_id: "178414" + Math.floor(Math.random() * 1000000000),
        last_message: text,
        last_message_time: new Date().toISOString(),
        messages: []
      };
      this.chats.push(chat);
    }

    const newMsg = {
      id: "msg_" + Math.random().toString(36).substr(2, 9),
      body: text,
      sender: "outbound",
      timestamp: new Date().toISOString(),
      reactions: [],
      note: "Private reply to comment"
    };

    chat.messages.push(newMsg);
    chat.last_message = text;
    chat.last_message_time = newMsg.timestamp;

    return { success: true, chat_id: chat.chat_id };
  }

  async createPost(postObj) {
    await this.delay(800); // Simulate processing delay
    const newPost = {
      id: "post_" + Math.random().toString(36).substr(2, 9),
      format: postObj.platforms?.instagram?.format || "post",
      body: postObj.post?.body || "",
      media: postObj.media || ["https://picsum.photos/id/400/800/800"],
      first_comment: postObj.platforms?.instagram?.first_comment || "",
      created_at: new Date().toISOString(),
      stats: {
        impressions: 0,
        likes: 0,
        comments: 0,
        saved: 0,
        profile_visits: 0,
        follows: 0
      }
    };

    // If reel, include cover url or name
    if (newPost.format === "reel") {
      newPost.cover_url = postObj.platforms?.instagram?.cover_url || "https://picsum.photos/id/60/400/700";
      newPost.audio_name = postObj.platforms?.instagram?.audio_name || "Original Audio";
    }

    this.posts.unshift(newPost); // Add to beginning of list
    return { success: true, post: newPost };
  }
}

export const mockDb = new MockDatabase();
