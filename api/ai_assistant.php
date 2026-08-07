<?php
async function sendAIMessage() {
    const input = document.getElementById('ai-input');
    const message = input.value.trim();
    if (!message) return;

    const chatMessages = document.getElementById('chat-messages');

    // عرض رسالة المستخدم
    chatMessages.innerHTML += `<div class="bg-indigo-600 text-white p-3 rounded-lg max-w-[85%] ml-auto text-right mb-2">${message}</div>`;
    input.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // حالة التحميل
    const loadingId = 'loading-' + Date.now();
    chatMessages.innerHTML += `<div id="${loadingId}" class="bg-slate-700 text-slate-200 p-3 rounded-lg max-w-[85%] mb-2">جاري التفكير...</div>`;

    try {
        const response = await fetch('api/ai_assistant.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            // إرسال سلة المشتريات الحالية والرسالة
            body: JSON.stringify({ 
                message: message,
                cart: cart 
            })
        });
        const data = await response.json();
        
        document.getElementById(loadingId).innerText = data.reply || data.error;
    } catch (e) {
        document.getElementById(loadingId).innerText = "حدث خطأ في الاتصال بالخادم.";
    }
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
?>