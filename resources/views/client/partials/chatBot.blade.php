<div id="chatbot-container">
    <div id="chat-header">
        <h4>CSKH Green Home</h4>
        <span class="close-btn" onclick="toggleChatbot()"><i class="ri-close-line"></i></span>
    </div>
    <div id="chat-body">
        <div class="message-container bot">
            <div class="message-box bot" style="display: flex; align-items: center;">
                <dotlottie-wc src="https://lottie.host/910bb217-5d6e-406d-80f8-2c78c0dc6cac/3ibnscHlHZ.lottie"
                    style="width: 80px;height: 60px; margin-right: 10px;" speed="1" autoplay loop>
                </dotlottie-wc>
                <span>Xin chào! Tôi có thể giúp gì cho bạn?</span>
            </div>
        </div>
    </div>
    <div id="chat-footer">
        <form id="chat-form">
            <input type="text" id="user-input" placeholder="Nhập tin nhắn...">
            <button type="submit" class="send-btn"><i class="ri-send-plane-fill"></i></button>
        </form>
    </div>
</div>

<div class="chat-toggle-btn" onclick="toggleChatbot()">
    <dotlottie-wc src="https://lottie.host/b3c296b0-4ee3-44d5-af72-dd9091a410d4/6kxlNEK1rA.lottie"
        style="width: 300px;height: 300px" speed="1" autoplay loop>
    </dotlottie-wc>
</div>

<script>
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatToggleButton = document.querySelector('.chat-toggle-btn');
    const chatBody = document.getElementById('chat-body');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');

    function toggleChatbot() {
        if (chatbotContainer.style.display === 'flex') {
            chatbotContainer.style.display = 'none';
            chatToggleButton.style.display = 'flex';
        } else {
            chatbotContainer.style.display = 'flex';
            chatToggleButton.style.display = 'none';
        }
    }

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

                    // CHỈNH SỬA TẠI ĐÂY: Thêm phần hiển thị sản phẩm nếu có
                    if (data.suggested_products && data.suggested_products.length > 0) {
                        const productsContainer = document.createElement('div');
                        productsContainer.className = 'suggested-products-container';
                        productsContainer.style.cssText =
                            " background-color: #f7f7f8; border-radius: 10px;";

                        data.suggested_products.forEach(product => {
                            const productItem = document.createElement('div');
                            productItem.className = 'product-item';
                            productItem.style.cssText =
                                "display: flex; align-items: center; border-bottom: 1px solid #ddd;";
                            productItem.innerHTML = `
                            <a href="/products/${product.slug}" 
                                style=" align-items: center; text-decoration: none; color: inherit; padding: 10px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 10px; transition: box-shadow 0.2s;">
                                    
                                    <!-- Hình ảnh sản phẩm -->
                                    <img src="${product.image}" alt="" 
                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; margin-right: 15px;">

                                    <!-- Thông tin sản phẩm -->
                                    <div class="product-info" style="flex: 1;">
                                        <h6 class="product-name" style="margin: 0 0 5px; font-size: 15px; font-weight: 600; color: #333;">
                                            ${product.name}
                                        </h6>
                                        <p class="product-views" style="margin: 0 0 8px; font-size: 12px; color: #777;">
                                            👁 Lượt xem: ${product.view}
                                        </p>
                                        <a href="/san-pham/${product.slug}" 
                                        class="btn btn-primary" 
                                        style="display: inline-block; font-size: 13px; border-radius: 5px; background-color: #007bff; color: #fff; text-decoration: none; transition: background-color 0.2s;">
                                            Xem
                                        </a>
                                    </div>
                                </a>
                        `;
                            productsContainer.appendChild(productItem);
                        });
                        chatBody.appendChild(productsContainer);
                    }
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
