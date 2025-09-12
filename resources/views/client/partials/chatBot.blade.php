{{-- Enhanced Chat Bot Styles --}}
<style>
    /* Chat Bot Container */
    #chatbot-container {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 350px;
        height: 500px;
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        display: none;
        flex-direction: column;
        z-index: 999;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* Chat Header */
    #chat-header {
        background: #4CAF50;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-logo {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .header-text {
        display: flex;
        flex-direction: column;
    }

    #chat-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .online-status {
        font-size: 12px;
        color: #e0f2e1;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .online-status::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #4cff5e;
        border-radius: 50%;
        display: inline-block;
    }

    /* Quick Replies */
    .quick-replies {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .quick-reply-btn {
        background: #ffffff;
        border: 1px solid #4CAF50;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 13px;
        color: #4CAF50;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quick-reply-btn:hover {
        background: #4CAF50;
        color: white;
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        background: #e9ecef;
        border-radius: 12px;
        width: fit-content;
        margin-bottom: 8px;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #6c757d;
        border-radius: 50%;
        display: inline-block;
        animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    .close-btn {
        cursor: pointer;
        padding: 5px;
    }

    .close-btn:hover {
        opacity: 0.8;
    }

    /* Chat Body */
    #chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        scroll-behavior: smooth;
        background: #f8f9fa;
    }

    /* Messages */
    .message-container {
        margin-bottom: 15px;
    }

    .message-box {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        margin: 5px 0;
        word-wrap: break-word;
        font-size: 14px;
        line-height: 1.4;
    }

    .message-box.bot {
        background: #e9ecef;
        margin-right: auto;
        border-bottom-left-radius: 5px;
    }

    .message-box.user {
        background: #4CAF50;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 5px;
    }

    /* Chat Footer */
    #chat-footer {
        padding: 15px;
        background: #ffffff;
        border-top: 1px solid #dee2e6;
    }

    #chat-form {
        display: flex;
        gap: 10px;
    }

    #user-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        border-radius: 20px;
        outline: none;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    #user-input:focus {
        border-color: #4CAF50;
    }

    .send-btn {
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }

    .send-btn:hover {
        background: #45a049;
    }

    /* Toggle Button */
    .chat-toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        width: 300px;
        height: 300px;
    }

    /* Product Suggestions */
    .suggested-products-container {
        margin-top: 10px;
        padding: 10px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .product-item {
        margin-bottom: 10px;
        transition: transform 0.2s;
    }

    .product-item:hover {
        transform: translateY(-2px);
    }

    .product-link {
        display: flex;
        align-items: center;
        padding: 8px;
        text-decoration: none;
        color: inherit;
        border: 1px solid #eee;
        border-radius: 8px;
    }

    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 12px;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .product-views {
        margin: 0 0 6px;
        font-size: 12px;
        color: #666;
    }

    .view-product-btn {
        padding: 4px 12px;
        font-size: 12px;
        color: #4CAF50;
        background: #e8f5e9;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .view-product-btn:hover {
        background: #c8e6c9;
    }

    /* Loading Animation */
    .loading-dots {
        display: inline-block;
        animation: loading 1.4s infinite;
    }

    @keyframes loading {
        0%, 20% { content: '.'; }
        40%, 60% { content: '..'; }
        80%, 100% { content: '...'; }
    }

    /* Scrollbar Customization */
    #chat-body::-webkit-scrollbar {
        width: 6px;
    }

    #chat-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #chat-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    #chat-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Cart Container */
    .cart-container {
        position: relative;
        z-index: 1000;
    }
</style>
<div id="chatbot-container">
    <div id="chat-header">
        <div class="header-content">
            <img src="{{ asset('assets_client/assets/img/logo/GreenHome_logo.png') }}" alt="Green Home Logo" class="chat-logo">
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

    // Chat History Management
    let chatHistory = localStorage.getItem('chatHistory') ? 
        JSON.parse(localStorage.getItem('chatHistory')) : [];
    const MAX_HISTORY = 50;

    // Initialize chat
    function initializeChat() {
        // Load chat history
        if (chatHistory.length > 0) {
            chatHistory.forEach(message => {
                appendMessage(message.type, message.content, message.products);
            });
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    // Toggle chatbot visibility
    function toggleChatbot() {
        if (chatbotContainer.style.display === 'flex') {
            chatbotContainer.style.display = 'none';
            chatToggleButton.style.display = 'flex';
        } else {
            chatbotContainer.style.display = 'flex';
            chatToggleButton.style.display = 'none';
            userInput.focus();
        }
    }

    // Send quick reply
    function sendQuickReply(message) {
        userInput.value = message;
        handleUserMessage(message);
    }

    // Handle user message
    function handleUserMessage(message) {
        if (!message.trim()) return;

        // Add user message to chat
        appendMessage('user', message);
        saveToHistory('user', message);

        // Clear input and show typing indicator
        userInput.value = '';
        showTypingIndicator();

        // Send to server
        fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
        .then(handleResponse)
        .then(handleData)
        .catch(handleError)
        .finally(() => {
            hideTypingIndicator();
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    }

    // Response handlers
    function handleResponse(response) {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    }

    function handleData(data) {
        // Add AI response to chat
        appendMessage('bot', data.ai_response, data.suggested_products);
        saveToHistory('bot', data.ai_response, data.suggested_products);
    }

    function handleError(error) {
        console.error('Error:', error);
        appendMessage('bot', 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!');
        saveToHistory('bot', 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!');
    }

    // UI Helpers
    function showTypingIndicator() {
        typingIndicator.style.display = 'flex';
    }

    function hideTypingIndicator() {
        typingIndicator.style.display = 'none';
    }

    function appendMessage(type, content, products = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-container ${type}`;
        
        const messageContent = `
            <div class="message-box ${type}">
                ${type === 'bot' ? 
                    `<div class="bot-avatar">
                        <dotlottie-wc src="https://lottie.host/910bb217-5d6e-406d-80f8-2c78c0dc6cac/3ibnscHlHZ.lottie" 
                            style="width: 80px;height: 60px;" speed="1" autoplay loop>
                        </dotlottie-wc>
                    </div>` : ''
                }
                <div class="message-content">
                    <span>${content}</span>
                </div>
            </div>
        `;
        
        messageDiv.innerHTML = messageContent;
        chatBody.appendChild(messageDiv);
        
        if (products) {
            appendProducts(products);
        }
    }

    function appendProducts(products) {
        const productsContainer = document.createElement('div');
        productsContainer.className = 'suggested-products-container';
        
        products.forEach(product => {
            const productItem = document.createElement('div');
            productItem.className = 'product-item';
            productItem.innerHTML = `
                <a href="/products/${product.slug}" class="product-link">
                    <img src="${product.image}" alt="${product.name}" class="product-image">
                    <div class="product-info">
                        <h6 class="product-name">${product.name}</h6>
                        <p class="product-views">👁 Lượt xem: ${product.view}</p>
                        <button class="view-product-btn">Xem chi tiết</button>
                    </div>
                </a>
            `;
            productsContainer.appendChild(productItem);
        });
        
        chatBody.appendChild(productsContainer);
    }

    // History Management
    function saveToHistory(type, content, products = null) {
        chatHistory.push({ type, content, products });
        if (chatHistory.length > MAX_HISTORY) {
            chatHistory.shift();
        }
        localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
    }

    // Event Listeners
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handleUserMessage(userInput.value);
    });

    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleUserMessage(userInput.value);
        }
    });

    // Initialize chat on load
    initializeChat();

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const userMessage = userInput.value.trim();
        if (userMessage) {
            // Thêm tin nhắn của người dùng vào chat body
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'message-container user';
            userMessageDiv.innerHTML = `<div class="message-box user">${userMessage}</div>`;
            chatBody.appendChild(userMessageDiv);

            // Xóa nội dung input và cuộn xuống cuối
            userInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Thêm hiệu ứng loading của bot
            const botLoadingDiv = document.createElement('div');
            botLoadingDiv.className = 'message-container bot';
            botLoadingDiv.innerHTML =
                `<div class="message-box bot"><span class="loading-dots">...</span></div>`;
            chatBody.appendChild(botLoadingDiv);
            chatBody.scrollTop = chatBody.scrollHeight;

            // Gửi tin nhắn đến server
            fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        message: userMessage
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    // CHỈNH SỬA TẠI ĐÂY: Chuyển đổi phản hồi từ server thành JSON
                    return response.json();
                })
                .then(data => {
                    // Xóa hiệu ứng loading
                    botLoadingDiv.remove();

                    // Thêm tin nhắn từ AI
                    const botMessageDiv = document.createElement('div');
                    botMessageDiv.className = 'message-container bot';
                    botMessageDiv.innerHTML = `
                    <div class="message-box bot" style="display: flex; align-items: center;">
                        <dotlottie-wc src="https://lottie.host/910bb217-5d6e-406d-80f8-2c78c0dc6cac/3ibnscHlHZ.lottie" style="width: 80px;height: 60px; margin-right: 10px;" speed="1" autoplay loop></dotlottie-wc>
                        <span>${data.ai_response}</span>
                    </div>
                `;
                    chatBody.appendChild(botMessageDiv);
                    chatBody.scrollTop = chatBody.scrollHeight;
                })
                .catch(error => {
                    console.error('Error:', error);
                    botLoadingDiv.remove();
                    const errorMessageDiv = document.createElement('div');
                    errorMessageDiv.className = 'message-container bot';
                    errorMessageDiv.innerHTML =
                        `<div class="message-box bot">Đã có lỗi xảy ra. Vui lòng thử lại sau.</div>`;
                    chatBody.appendChild(errorMessageDiv);
                    chatBody.scrollTop = chatBody.scrollHeight;
                });
        }
    });
</script>

<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
