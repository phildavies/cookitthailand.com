window.addEventListener('DOMContentLoaded', async () => {

    const url = Joomla.getOptions('system.paths').rootFull + 'index.php?option=com_route66&task=page.discover&format=json';
    const options = Joomla.getOptions('route66.discover');

    const payload = {
        title: options.title,
        description: options.description,
        uri: options.uri,
        hash: options.hash,
        [window.Joomla.getOptions('csrf.token')]: 1
    }

    try {
        await fetch(url, {
            method: 'POST',
            body: JSON.stringify(payload),
        });

    } catch (error) {
        console.error(error.message);
    }
});