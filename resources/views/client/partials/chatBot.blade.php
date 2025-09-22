{{-- Enhanced Chat Bot Styles --}}
<style>
    {{-- Enhanced Chat Bot Styles - Version 2.0 --}} <style>

    /* Chat Bot Container */
    #chatbot-container {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 380px;
        height: 550px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        z-index: 999;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
    }

    #chatbot-container.show {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    #chatbot-container.hide {
        transform: translateY(20px) scale(0.95);
        opacity: 0;
    }

    /* Chat Header */
    #chat-header {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        position: relative;
        overflow: hidden;
    }

    #chat-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: headerShine 6s linear infinite;
    }

    @keyframes headerShine {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 2;
        position: relative;
    }

    .chat-logo {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: transform 0.3s ease;
    }

    .chat-logo:hover {
        transform: scale(1.1) rotate(5deg);
    }

    .header-text {
        display: flex;
        flex-direction: column;
    }

    #chat-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .online-status {
        font-size: 12px;
        color: #e0f2e1;
        display: flex;
        align-items: center;
        gap: 6px;
        opacity: 0.9;
    }

    .online-status::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #4cff5e;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s infinite;
        box-shadow: 0 0 5px rgba(76, 255, 94, 0.5);
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Quick Replies */
    .quick-replies {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 15px;
        animation: slideUp 0.5s ease 0.3s both;
    }

    .quick-reply-btn {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #4CAF50;
        border-radius: 25px;
        padding: 10px 16px;
        font-size: 12px;
        color: #4CAF50;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .quick-reply-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }

    .quick-reply-btn:hover {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
    }

    .quick-reply-btn:hover::before {
        left: 100%;
    }

    .quick-reply-btn:active {
        transform: translateY(0);
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        border-radius: 18px;
        width: fit-content;
        margin-bottom: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border-radius: 50%;
        display: inline-block;
        animation: typing 1.6s infinite ease-in-out;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(0.8) translateY(0);
            opacity: 0.5;
        }

        40% {
            transform: scale(1.2) translateY(-8px);
            opacity: 1;
        }
    }

    .close-btn {
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
        z-index: 3;
        position: relative;
        background: rgba(255, 255, 255, 0.1);
    }

    .close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    /* Chat Body */
    #chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        scroll-behavior: smooth;
        background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        position: relative;
    }

    /* Custom Scrollbar */
    #chat-body::-webkit-scrollbar {
        width: 6px;
    }

    #chat-body::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }

    #chat-body::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #4CAF50 0%, #45a049 100%);
        border-radius: 3px;
        transition: background 0.3s ease;
    }

    #chat-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #45a049 0%, #388e3c 100%);
    }

    /* Messages */
    .message-container {
        margin-bottom: 20px;
        animation: messageSlide 0.4s ease;
    }

    @keyframes messageSlide {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-box {
        max-width: 85%;
        padding: 14px 18px;
        border-radius: 20px;
        margin: 8px 0;
        word-wrap: break-word;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .message-box.bot {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        margin-right: auto;
        border-bottom-left-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .message-box.user {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 8px;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .message-box.bot .message-content {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .bot-avatar {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .message-time {
        font-size: 10px;
        opacity: 0.6;
        margin-top: 5px;
        text-align: right;
    }

    /* Chat Footer */
    #chat-footer {
        padding: 20px;
        background: #ffffff;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    #chat-form {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    #user-input {
        flex: 1;
        padding: 12px 18px;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        outline: none;
        font-size: 14px;
        transition: all 0.3s ease;
        resize: none;
        min-height: 20px;
        max-height: 100px;
        font-family: inherit;
    }

    #user-input:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        transform: translateY(-1px);
    }

    #user-input:disabled {
        background: #f8f9fa;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .send-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        border: none;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        position: relative;
        overflow: hidden;
    }

    .send-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transition: all 0.3s ease;
        transform: translate(-50%, -50%);
    }

    .send-btn:hover {
        background: linear-gradient(135deg, #45a049 0%, #388e3c 100%);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    .send-btn:hover::before {
        width: 100%;
        height: 100%;
    }

    .send-btn:active {
        transform: translateY(0) scale(1);
    }

    .send-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Toggle Button */
    .chat-toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        width: 300px;
        height: 300px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    /* Product Suggestions */
        .suggested-products-container {
        margin: 10px 0;
        padding: 10px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        max-width: 100%;
        display: block !important;
        z-index: 1000;
        position: relative;
    }
        margin-top: 15px;
        padding: 16px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(76, 175, 80, 0.1);
        position: relative;
        overflow: hidden;
    }

    .suggested-products-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #4CAF50 0%, #45a049 50%, #4CAF50 100%);
        background-size: 200% 100%;
        animation: productHeaderShine 3s linear infinite;
    }

    @keyframes productHeaderShine {
        0% {
            background-position: -200% 0;
        }

        100% {
            background-position: 200% 0;
        }
    }

    .product-item {
        margin-bottom: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        overflow: hidden;
    }

    .product-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .product-item:last-child {
        margin-bottom: 0;
    }

    .product-link {
        display: flex;
        align-items: center;
        padding: 12px;
        text-decoration: none;
        color: inherit;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .product-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(76, 175, 80, 0.05), transparent);
        transition: left 0.6s ease;
    }

    .product-link:hover::before {
        left: 100%;
    }

    .product-link:hover {
        border-color: #4CAF50;
        transform: translateX(5px);
    }

    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        margin-right: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .product-link:hover .product-image {
        transform: scale(1.05);
    }

    .product-info {
        flex: 1;
        min-width: 0;
    }

    .product-name {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-views {
        margin: 0 0 8px;
        font-size: 11px;
        color: #7f8c8d;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .view-product-btn {
        padding: 6px 14px;
        font-size: 11px;
        color: #4CAF50;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border: 1px solid #4CAF50;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .view-product-btn:hover {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
    }

    /* Error and Success Messages */
    .error-message,
    .success-message {
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        #chatbot-container {
            width: calc(100vw - 20px);
            height: calc(100vh - 40px);
            bottom: 10px;
            right: 10px;
            border-radius: 15px;
        }

        .chat-toggle-btn {
            width: 200px;
            height: 200px;
        }

        .product-link {
            flex-direction: column;
            text-align: center;
        }

        .product-image {
            margin: 0 0 10px 0;
        }
    }

    /* Animation for slide up */
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Welcome message special styling */
    .welcome-message {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%) !important;
        border-left: 4px solid #4CAF50;
    }

    .welcome-message .sub-message {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        opacity: 0.8;
    }
</style>
</style>
<div id="chatbot-container">
    <div id="chat-header">
        <div class="header-content">
            <img src="{{ asset('assets_client/assets/img/logo/GreenHome_logo.png') }}" alt="Green Home Logo"
                class="chat-logo">
            <div class="header-text">
                <h4>CSKH Green Home</h4>
                <span class="online-status">● Đang hoạt động</span>
            </div>
        </div>
        <span class="close-btn" onclick="toggleChatbot()"><i class="ri-close-line"></i></span>
    </div>

    <div id="chat-body">
        <div class="message-container bot">
            <div class="message-box bot welcome-message">
                <div class="bot-avatar">
                    <dotlottie-wc src="https://lottie.host/910bb217-5d6e-406d-80f8-2c78c0dc6cac/3ibnscHlHZ.lottie"
                        style="width: 80px;height: 60px;" speed="1" autoplay loop>
                    </dotlottie-wc>
                </div>
                <div class="message-content">
                    <span>Xin chào! Tôi là trợ lý Green Home 🌱</span>
                    <span class="sub-message">Tôi có thể giúp bạn:</span>
                    <div class="quick-replies">
                        <button onclick="sendQuickReply('Tìm sản phẩm thân thiện môi trường')" class="quick-reply-btn">
                            🔍 Tìm sản phẩm xanh
                        </button>
                        <button onclick="sendQuickReply('Tư vấn về sản phẩm sinh thái')" class="quick-reply-btn">
                            💡 Tư vấn sản phẩm
                        </button>
                        <button onclick="sendQuickReply('Chính sách và ưu đãi')" class="quick-reply-btn">
                            🎁 Xem ưu đãi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chat-footer">
        <div class="typing-indicator" style="display: none;">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <form id="chat-form">
            <input type="text" id="user-input" placeholder="Nhập tin nhắn của bạn...">
            <button type="submit" class="send-btn" title="Gửi tin nhắn">
                <i class="ri-send-plane-fill"></i>
            </button>
        </form>
    </div>
</div>

<div class="chat-toggle-btn" onclick="toggleChatbot()">
    <dotlottie-wc src="https://lottie.host/b3c296b0-4ee3-44d5-af72-dd9091a410d4/6kxlNEK1rA.lottie"
        style="width: 300px;height: 300px" speed="1" autoplay loop>
    </dotlottie-wc>
</div>

<script>
    // DOM Elements
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatToggleButton = document.querySelector('.chat-toggle-btn');
    const chatBody = document.getElementById('chat-body');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const typingIndicator = document.querySelector('.typing-indicator');

    // Lưu đoạn hội thoại vào localStorage
    let chatHistory = localStorage.getItem('greenHomeChatHistory') ?
        JSON.parse(localStorage.getItem('greenHomeChatHistory')) : [];
    const MAX_HISTORY = 20; // Tối đa chỉ lưu 20 tin nhắn

    // Sự kiện gửi tin nhắn
    function initializeChat() {
        // load 10 tin nhắn gần nhất
        const recentHistory = chatHistory.slice(-10);
        recentHistory.forEach(message => {
            appendMessage(message.type, message.content, message.products, false);
        });
        if (recentHistory.length > 0) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    // Lưu tin nhắn vào lịch sử
    function toggleChatbot() {
        if (chatbotContainer.style.display === 'flex') {
            chatbotContainer.style.display = 'none';
            chatToggleButton.style.display = 'flex';
        } else {
            chatbotContainer.style.display = 'flex';
            chatToggleButton.style.display = 'none';
            userInput.focus();

            // Mark as read (có thể thêm tính năng này sau)
            // markMessagesAsRead();
        }
    }

    // Gửi phản hồi nhanh
    function sendQuickReply(message) {
        userInput.value = message;
        handleUserMessage(message);
    }

    // Handle user message - Cải tiến với validation tốt hơn
    function handleUserMessage(message) {
        const trimmedMessage = message.trim();

        // Validation
        if (!trimmedMessage || trimmedMessage.length < 2) {
            showErrorMessage('Vui lòng nhập tin nhắn hợp lệ!');
            return;
        }

        if (trimmedMessage.length > 500) {
            showErrorMessage('Tin nhắn quá dài! Vui lòng nhập ít hơn 500 ký tự.');
            return;
        }

        // Add user message to chat
        appendMessage('user', trimmedMessage, null, true);
        saveToHistory('user', trimmedMessage);

        // Clear input và disable để tránh spam
        userInput.value = '';
        userInput.disabled = true;

        // Show typing indicator
        showTypingIndicator();

        // Send to server với timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000); // 15s timeout

        fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: trimmedMessage
                }),
                signal: controller.signal
            })
            .then(handleResponse)
            .then(handleData)
            .catch(handleError)
            .finally(() => {
                clearTimeout(timeoutId);
                hideTypingIndicator();
                userInput.disabled = false;
                userInput.focus();
                scrollToBottom();
            });
    }

    // Response handlers - Cải tiến
    function handleResponse(response) {
        if (!response.ok) {
            if (response.status === 429) {
                throw new Error('Bạn đang gửi tin nhắn quá nhanh. Vui lòng chờ một chút!');
            }
            if (response.status === 500) {
                throw new Error('Lỗi server. Vui lòng thử lại sau!');
            }
            throw new Error(`HTTP Error: ${response.status}`);
        }
        return response.json();
    }

    function handleData(data) {
        if (!data.ai_response) {
            throw new Error('Phản hồi không hợp lệ từ server');
        }

        // Add AI response to chat
        appendMessage('bot', data.ai_response, data.suggested_products || [], true);
        saveToHistory('bot', data.ai_response, data.suggested_products || []);

        // Analytics tracking (có thể thêm)
        // trackChatInteraction('bot_response', data.ai_response.length);
    }

    // Lưu tin nhắn vào lịch sử với giới hạn
    function handleError(error) {
        console.error('Chat Error:', error);

        let errorMessage = 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!';

        if (error.name === 'AbortError') {
            errorMessage = 'Kết nối bị gián đoạn. Vui lòng kiểm tra mạng và thử lại!';
        } else if (error.message.includes('fetch')) {
            errorMessage = 'Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng!';
        } else if (error.message) {
            errorMessage = error.message;
        }

        appendMessage('bot', errorMessage, [], true);
        saveToHistory('bot', errorMessage);
    }

    // UI Helpers - Cải tiến
    function showTypingIndicator() {
        typingIndicator.style.display = 'flex';
        scrollToBottom();
    }

    function hideTypingIndicator() {
        typingIndicator.style.display = 'none';
    }

    function showErrorMessage(message) {
        // Hiển thị error message tạm thời
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: #f44336;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
    `;
        errorDiv.textContent = message;

        chatbotContainer.appendChild(errorDiv);

        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 3000);
    }

    function scrollToBottom() {
        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 100);
    }

    // Cải tiến appendMessage với animation
    function appendMessage(type, content, products = [], shouldScroll = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-container ${type}`;

        // Tạo unique ID cho message
        const messageId = Date.now() + Math.random().toString(36).substr(2, 9);
        messageDiv.dataset.messageId = messageId;

        const messageContent = `
        <div class="message-box ${type}" style="opacity: 0; transform: translateY(10px); transition: all 0.3s ease;">
            ${type === 'bot' ? 
                `<div class="bot-avatar">
                    <dotlottie-wc src="https://lottie.host/910bb217-5d6e-406d-80f8-2c78c0dc6cac/3ibnscHlHZ.lottie" 
                        style="width: 30px; height: 30px;" speed="1" autoplay loop>
                    </dotlottie-wc>
                </div>` : ''
            }
            <div class="message-content">
                <span>${sanitizeHtml(content)}</span>
                ${type === 'bot' ? '<div class="message-time">' + new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) + '</div>' : ''}
            </div>
        </div>
    `;

        messageDiv.innerHTML = messageContent;
        chatBody.appendChild(messageDiv);

        // Animation hiện message
        setTimeout(() => {
            const messageBox = messageDiv.querySelector('.message-box');
            messageBox.style.opacity = '1';
            messageBox.style.transform = 'translateY(0)';
        }, 50);

        // Hiển thị sản phẩm nếu có và type là bot
        if (products && products.length > 0 && type === 'bot') {
            setTimeout(() => {
                appendProducts(products);
            }, 300);
        }

        if (shouldScroll) {
            scrollToBottom();
        }
    }

    // Cải tiến appendProducts với validation
    function appendProducts(products) {
        console.log('Appending products:', products); // Debug log
        
        if (!products || !Array.isArray(products) || products.length === 0) {
            console.log('No valid products to display'); // Debug log
            return;
        }

        const productsContainer = document.createElement('div');
        productsContainer.className = 'suggested-products-container';
        productsContainer.style.cssText = 'opacity: 0; transform: translateY(20px); transition: all 0.4s ease; display: block !important; margin-top: 10px;';

        const validProducts = products.filter(product =>
            product &&
            product.name &&
            product.slug &&
            typeof product.id !== 'undefined'
        );

        if (validProducts.length === 0) {
            return;
        }

        // Header cho sản phẩm gợi ý
        const headerDiv = document.createElement('div');
        headerDiv.style.cssText = 'margin-bottom: 10px; font-weight: 500; color: #4CAF50; font-size: 13px;';
        headerDiv.innerHTML = `<i class="ri-lightbulb-line"></i> Sản phẩm gợi ý cho bạn:`;
        productsContainer.appendChild(headerDiv);

        validProducts.slice(0, 3).forEach((product, index) => {
            const productItem = document.createElement('div');
            productItem.className = 'product-item';
            productItem.style.cssText = `
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.3s ease ${index * 0.1}s;
        `;

            const productImage = product.image && product.image !== 'null' ?
                product.image :
                '/assets_client/assets/img/default-product.jpg';

            productItem.innerHTML = `
            <a href="/products/${product.slug}" class="product-link" target="_blank">
                <img src="${productImage}" alt="${sanitizeHtml(product.name)}" class="product-image" 
                     onerror="this.src='/assets_client/assets/img/default-product.jpg'">
                <div class="product-info">
                    <h6 class="product-name">${sanitizeHtml(product.name)}</h6>
                    <p class="product-views">👁️ Lượt xem: ${product.view || 0}</p>
                    <button type="button" class="view-product-btn" onclick="trackProductView(${product.id})">
                        Xem chi tiết →
                    </button>
                </div>
            </a>
        `;
            productsContainer.appendChild(productItem);

            // Animation cho từng sản phẩm
            setTimeout(() => {
                productItem.style.opacity = '1';
                productItem.style.transform = 'translateX(0)';
            }, 100 + (index * 100));
        });

        chatBody.appendChild(productsContainer);

        // Animation cho container với delay ngắn hơn
        requestAnimationFrame(() => {
            productsContainer.style.opacity = '1';
            productsContainer.style.transform = 'translateY(0)';
        });

        // Đảm bảo scroll sau khi animation hoàn tất
        setTimeout(() => {
            scrollToBottom();
            console.log('Products container added and scrolled'); // Debug log
        }, 300);
    }

    // Utility functions
    function sanitizeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function trackProductView(productId) {
        // Có thể implement tracking analytics ở đây
        console.log('Product viewed:', productId);

        // Gửi event tracking nếu cần
        // fetch('/api/track-product-view', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ product_id: productId, source: 'chatbot' })
        // });
    }

    // History Management - Cải tiến
    function saveToHistory(type, content, products = []) {
        const message = {
            type,
            content,
            products,
            timestamp: new Date().toISOString()
        };

        chatHistory.push(message);

        // Giới hạn lịch sử
        if (chatHistory.length > MAX_HISTORY) {
            chatHistory = chatHistory.slice(-MAX_HISTORY);
        }

        try {
            localStorage.setItem('greenHomeChatHistory', JSON.stringify(chatHistory));
        } catch (e) {
            console.warn('Cannot save to localStorage:', e);
            // Nếu localStorage đầy, xóa một phần lịch sử cũ
            chatHistory = chatHistory.slice(-10);
            try {
                localStorage.setItem('greenHomeChatHistory', JSON.stringify(chatHistory));
            } catch (e) {
                console.error('Cannot save to localStorage even after cleanup:', e);
            }
        }
    }

    function clearChatHistory() {
        if (confirm('Bạn có chắc muốn xóa lịch sử trò chuyện không?')) {
            chatHistory = [];
            localStorage.removeItem('greenHomeChatHistory');

            // Xóa tất cả messages trừ welcome message
            const messages = chatBody.querySelectorAll('.message-container:not(.welcome-message)');
            messages.forEach(msg => msg.remove());

            // Xóa product containers
            const productContainers = chatBody.querySelectorAll('.suggested-products-container');
            productContainers.forEach(container => container.remove());

            showSuccessMessage('Đã xóa lịch sử trò chuyện!');
        }
    }

    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.cssText = `
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: #4CAF50;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
        successDiv.textContent = message;

        chatbotContainer.appendChild(successDiv);

        // Animation hiện
        setTimeout(() => {
            successDiv.style.opacity = '1';
        }, 10);

        // Animation ẩn và xóa
        setTimeout(() => {
            successDiv.style.opacity = '0';
            setTimeout(() => {
                if (successDiv.parentNode) {
                    successDiv.parentNode.removeChild(successDiv);
                }
            }, 300);
        }, 2700);
    }

    // Keyboard shortcuts
    function handleKeyboardShortcuts(e) {
        // Ctrl/Cmd + Enter để gửi
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            const message = userInput.value.trim();
            if (message) {
                handleUserMessage(message);
            }
        }

        // ESC để đóng chatbot
        if (e.key === 'Escape' && chatbotContainer.style.display === 'flex') {
            toggleChatbot();
        }
    }

    // Auto-resize input
    function autoResizeInput() {
        userInput.style.height = 'auto';
        const newHeight = Math.min(userInput.scrollHeight, 100); // Max 100px
        userInput.style.height = newHeight + 'px';
    }

    // Detect user activity
    let isUserActive = true;
    let activityTimeout;

    function resetActivityTimer() {
        isUserActive = true;
        clearTimeout(activityTimeout);

        activityTimeout = setTimeout(() => {
            isUserActive = false;
        }, 30000); // 30 seconds
    }

    // Online/Offline detection
    function updateConnectionStatus() {
        const statusElement = document.querySelector('.online-status');
        if (statusElement) {
            if (navigator.onLine) {
                statusElement.innerHTML = '<span style="color: #4cff5e;">●</span> Đang hoạt động';
                statusElement.style.color = '#e0f2e1';
            } else {
                statusElement.innerHTML = '<span style="color: #ff4444;">●</span> Mất kết nối';
                statusElement.style.color = '#ffcdd2';
            }
        }
    }

    // Event Listeners
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = userInput.value.trim();
        if (message) {
            handleUserMessage(message);
        }
    });

    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const message = userInput.value.trim();
            if (message) {
                handleUserMessage(message);
            }
        }
    });

    userInput.addEventListener('input', function() {
        autoResizeInput();
        resetActivityTimer();
    });

    // Global keyboard shortcuts
    document.addEventListener('keydown', handleKeyboardShortcuts);

    // Activity tracking
    document.addEventListener('click', resetActivityTimer);
    document.addEventListener('scroll', resetActivityTimer);
    document.addEventListener('mousemove', resetActivityTimer);

    // Connection status
    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);

    // Page visibility
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && chatbotContainer.style.display === 'flex') {
            userInput.focus();
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        // Cleanup any pending requests
        // Save any unsaved data
    });

    // Initialize chat on load
    document.addEventListener('DOMContentLoaded', function() {
        initializeChat();
        updateConnectionStatus();
        resetActivityTimer();

        // Focus input if chatbot is open
        if (chatbotContainer.style.display === 'flex') {
            userInput.focus();
        }

        // Add clear history button to header (optional)
        const headerContent = document.querySelector('.header-content');
        if (headerContent) {
            const clearBtn = document.createElement('button');
            clearBtn.innerHTML = '<i class="ri-delete-bin-line"></i>';
            clearBtn.style.cssText = `
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
            font-size: 14px;
            transition: background 0.2s;
            margin-left: 10px;
        `;
            clearBtn.title = 'Xóa lịch sử trò chuyện';
            clearBtn.onclick = clearChatHistory;
            clearBtn.onmouseover = function() {
                this.style.background = 'rgba(255,255,255,0.2)';
            };
            clearBtn.onmouseout = function() {
                this.style.background = 'none';
            };

            // Thêm vào header
            headerContent.appendChild(clearBtn);
        }
    });

    // Export functions for global access
    window.chatbotFunctions = {
        toggleChatbot,
        sendQuickReply,
        clearChatHistory,
        trackProductView
    };

    // Tự động xóa đoạn chat sau 24 giờ không hoạt động
    setInterval(() => {
        const now = new Date();
        chatHistory = chatHistory.filter(msg => {
            const msgTime = new Date(msg.timestamp);
            return (now - msgTime) < 24 * 60 * 60 * 1000; // 24 hours
        });
        localStorage.setItem('greenHomeChatHistory', JSON.stringify(chatHistory)); // Cập nhật lại lịch sử
    }, 60 * 60 * 1000); // Kiểm tra mỗi giờ
</script>

<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
