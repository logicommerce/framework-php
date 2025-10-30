LC.initQueue.enqueue(function(agents, userAgent) {
    agents = prepareAgents();
    userAgent = prepareUserAgent();

    if (isChecked()) return;

    var agentName, dimensions;
    for (var i = 0; i < agents.length; i++) {
        agentName = getAgentName(agents[i]);
        dimensions = getDimensions(agents[i]);

        if (agentName == '*' || userAgent.indexOf(agentName) > -1) {
            if (screen.width <= dimensions.width || screen.height <= dimensions.height) {
                if (agents[i].split('=')[1] == 0) {
                    setCookie();
                    changeLocation();
                }
                return;
            }
        }
    }

    function prepareAgents() {
        agents = agents || LC.global.settings.mobileAgents || '';
        return agents.toUpperCase().split(',');
    }

    function prepareUserAgent() {
        userAgent = userAgent || navigator.userAgent;
        return userAgent.toUpperCase();
    }

    function getAgentName(agent) {
        var agentName;

        if (agent.indexOf('[') > -1) agentName = agent.split('[')[0];
        else agentName = agent.split('=')[0];

        return agentName;
    }

    function getDimensions(agent) {
        var dimensions = '10000X10000';

        if (agent.indexOf('[') > -1) dimensions = agent.split('[')[1].split(']')[0];

        return {
            width: dimensions.split('X')[0] || 10000,
            height: dimensions.split('X')[1] || 10000,
        };
    }

    function isChecked() {
        if (location.search.indexOf('classic=') > -1) return true;

        if (window.hasOwnProperty('Cookies') && Cookies.get(COOKIE_GO_DESKTOP)) return true;

        return false;
    }

    function setCookie() {
        if (!window.hasOwnProperty('Cookies')) return;

        Cookies.set(COOKIE_GO_DESKTOP, '1');
    }

    function changeLocation() {
        var locationSearch = (location.search ? location.search + '&' : '?') + 'classic=true';
        location.href = location.pathname + locationSearch;
    }
});
