// Universal Coin Sync for Math Quest
(function() {
    console.log('💰 Coin Sync System Loaded');
    
    function fetchCoins() {
        fetch('/coins.php', {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const coins = data.coins;
                localStorage.setItem('mathQuest_coins', coins);
                
                // Update all coin displays
                document.querySelectorAll('#coinCount, #coinCountNav, #coinCountMap, #shopCoinTotal, #profileCoins').forEach(el => {
                    if (el) el.textContent = coins.toLocaleString();
                });
                
                window.dispatchEvent(new CustomEvent('coinsUpdated', { detail: { coins: coins } }));
            }
        })
        .catch(err => console.error('Fetch error:', err));
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fetchCoins);
    } else {
        fetchCoins();
    }
    
    setInterval(fetchCoins, 10000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) fetchCoins(); });
    window.refreshCoins = fetchCoins;
})();