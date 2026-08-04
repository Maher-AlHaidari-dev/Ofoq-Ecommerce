let cart = [];

// إضافة عنصر للسلة
function addToCart(id, name, price) {
    cart.push({ id, name, price });
    document.getElementById('cart-count').innerText = cart.length;
    alert(`تمت إضافة "${name}" إلى السلة بنجاح!`);
}

// إظهار/إخفاء نافذة المساعد الذكي
function toggleAIChat() {
    const chatBox = document.getElementById('ai-chat-box');
    chatBox.classList.toggle('hidden');
}

// إرسال سؤال للذكاء الاصطناعي
async function sendAIMessage() {
    const input = document.getElementById('ai-input');
    const message = input.value.trim();
    if (!message) return;

    const chatMessages = document.getElementById('chat-messages');

    // رسالة المستخدم
    chatMessages.innerHTML += `<div class="bg-white border text-gray-800 p-3 rounded-lg max-w-[85%] mr-auto text-left">${message}</div>`;
    input.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // جاري الرد
    const loadingId = 'loading-' + Date.now();
    chatMessages.innerHTML += `<div id="${loadingId}" class="bg-indigo-100 text-indigo-900 p-3 rounded-lg max-w-[85%]">جاري التفكير...</div>`;

    try {
        const response = await fetch('api/ai_assistant.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message })
        });
        const data = await response.json();
        
        document.getElementById(loadingId).innerText = data.reply;
    } catch (e) {
        document.getElementById(loadingId).innerText = "حدث خطأ في الاتصال بالخادم.";
    }
    chatMessages.scrollTop = chatMessages.scrollHeight;
}