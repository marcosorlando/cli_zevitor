// Simple message send (demo)
const msgForm = document.getElementById('messageForm');
if (msgForm) {
    msgForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const input = document.getElementById('messageInput');
        const val = input.value.trim();
        if (!val) return;

        const container = document.querySelector('.chat__window-body');

        // Create user message bubble
        const userBubble = document.createElement('div');
        userBubble.className = 'chat__msg chat__msg--me';

        const userImgDiv = document.createElement('div');
        userImgDiv.className = 'chat-mst-img';
        const userImg = document.createElement('img');
        userImg.src = 'assets/images/resources/chat-item-avator-1-2.jpg'; // your avatar
        userImg.alt = '';
        userImgDiv.appendChild(userImg);

        const userText = document.createElement('p');
        userText.textContent = val;

        userBubble.appendChild(userImgDiv);
        userBubble.appendChild(userText);
        container.appendChild(userBubble);
        input.value = '';
        container.scrollTop = container.scrollHeight;

        // Fake reply with full style
        setTimeout(() => {
            const replyBubble = document.createElement('div');
            replyBubble.className = 'chat__msg chat__msg--other';

            const replyImgDiv = document.createElement('div');
            replyImgDiv.className = 'chat-mst-img';
            const replyImg = document.createElement('img');
            replyImg.src = 'assets/images/resources/chat-item-avator-1-1.jpg'; // reply avatar
            replyImg.alt = '';
            replyImgDiv.appendChild(replyImg);

            const replyText = document.createElement('p');
            replyText.textContent = 'Thanks for your message!';

            replyBubble.appendChild(replyImgDiv);
            replyBubble.appendChild(replyText);
            container.appendChild(replyBubble);
            container.scrollTop = container.scrollHeight;
        }, 800);
    });
}