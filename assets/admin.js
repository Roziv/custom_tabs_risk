document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('custom-tabs-container');
    const addButton = document.getElementById('custom-tabs-add-tab');
    const dataInput = document.getElementById('custom_tabs_data');
    const form = document.getElementById('custom-tabs-form');

    // Parse existing data
    let tabsData = [];
    try {
        if (dataInput.value.trim() !== '') {
            tabsData = JSON.parse(dataInput.value);
        }
    } catch (e) {
        console.error('Failed to parse Custom Tabs data:', e);
    }

    // Initialize UI
    renderAllTabs();

    // Event listener for adding a new tab
    addButton.addEventListener('click', function() {
        const newTab = {
            id: 'tab_' + Date.now(),
            title: '',
            section1: { quote: '', image: '', name: '', title: '', logo: '' },
            section2: { box1: '', box2: '' },
            section3: { box1: '' }
        };
        tabsData.push(newTab);
        renderAllTabs();
    });

    // Update JSON before saving the form
    form.addEventListener('submit', function() {
        dataInput.value = JSON.stringify(tabsData);
    });

    /**
     * Re-renders the entire DOM based on the current tabsData state.
     */
    function renderAllTabs() {
        container.innerHTML = ''; // Clear container

        tabsData.forEach((tab, index) => {
            const tabEl = document.createElement('div');
            tabEl.className = 'custom-tab-item postbox';
            tabEl.dataset.index = index;

            // Header (Title area + Remove button)
            const header = document.createElement('div');
            header.className = 'postbox-header custom-tab-header';
            
            const titleInput = document.createElement('input');
            titleInput.type = 'text';
            titleInput.className = 'large-text custom-tab-title-input';
            titleInput.placeholder = 'Tab Title (e.g. Overview)';
            titleInput.value = tab.title;
            titleInput.addEventListener('input', (e) => {
                tabsData[index].title = e.target.value;
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'button-link custom-tab-remove';
            removeBtn.innerHTML = '<span class="dashicons dashicons-trash"></span> Remove Tab';
            removeBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to remove this tab?')) {
                    tabsData.splice(index, 1);
                    renderAllTabs();
                }
            });

            header.appendChild(titleInput);
            header.appendChild(removeBtn);
            tabEl.appendChild(header);

            // Body
            const body = document.createElement('div');
            body.className = 'inside custom-tab-body';

            // --- SECTION 1 ---
            const sec1 = document.createElement('div');
            sec1.className = 'custom-tab-section';
            sec1.innerHTML = '<h3>Section 1</h3>';

            sec1.appendChild(createField('textarea', 'Quote', tab.section1.quote, (val) => tabsData[index].section1.quote = val));
            sec1.appendChild(createMediaUploadField('Image', tab.section1.image, (val) => tabsData[index].section1.image = val));
            sec1.appendChild(createField('text', 'Name', tab.section1.name, (val) => tabsData[index].section1.name = val));
            sec1.appendChild(createField('text', 'Title', tab.section1.title, (val) => tabsData[index].section1.title = val));
            sec1.appendChild(createMediaUploadField('Logo', tab.section1.logo, (val) => tabsData[index].section1.logo = val));

            body.appendChild(sec1);

            // --- SECTION 2 ---
            const sec2 = document.createElement('div');
            sec2.className = 'custom-tab-section';
            sec2.innerHTML = '<h3>Section 2</h3>';
            tab.section2 = tab.section2 || {};
            sec2.appendChild(createField('text', 'Text Box 1', tab.section2.box1 || tab.section2.content || '', (val) => tabsData[index].section2.box1 = val));
            sec2.appendChild(createField('text', 'Text Box 2', tab.section2.box2 || '', (val) => tabsData[index].section2.box2 = val));
            body.appendChild(sec2);

            // --- SECTION 3 ---
            const sec3 = document.createElement('div');
            sec3.className = 'custom-tab-section';
            sec3.innerHTML = '<h3>Section 3</h3>';
            tab.section3 = tab.section3 || {};
            sec3.appendChild(createField('text', 'Text Box', tab.section3.box1 || tab.section3.content || '', (val) => tabsData[index].section3.box1 = val));
            body.appendChild(sec3);

            tabEl.appendChild(body);
            container.appendChild(tabEl);
        });
    }

    /**
     * Helper to create a standard text or textarea field.
     */
    function createField(type, labelText, value, onChangeCallback) {
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-tab-field row';

        const label = document.createElement('label');
        label.innerText = labelText;

        let input;
        if (type === 'textarea') {
            input = document.createElement('textarea');
            input.rows = 4;
            input.className = 'large-text';
            input.value = value;
        } else {
            input = document.createElement('input');
            input.type = 'text';
            input.className = 'regular-text';
            input.value = value;
        }

        input.addEventListener('input', (e) => onChangeCallback(e.target.value));

        wrapper.appendChild(label);
        wrapper.appendChild(input);
        return wrapper;
    }

    /**
     * Helper to create a WordPress Media Uploader field.
     */
    function createMediaUploadField(labelText, value, onChangeCallback) {
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-tab-field row media-row';

        const label = document.createElement('label');
        label.innerText = labelText;

        const urlInput = document.createElement('input');
        urlInput.type = 'text';
        urlInput.className = 'regular-text';
        urlInput.value = value;
        urlInput.placeholder = 'http://...';
        urlInput.addEventListener('input', (e) => onChangeCallback(e.target.value));

        const uploadBtn = document.createElement('button');
        uploadBtn.type = 'button';
        uploadBtn.className = 'button';
        uploadBtn.innerText = 'Select Image';

        // Integrate with WordPress Media Library
        uploadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const mediaUploader = wp.media({
                title: 'Select ' + labelText,
                button: { text: 'Use this image' },
                multiple: false
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                urlInput.value = attachment.url;
                onChangeCallback(attachment.url);
            });

            mediaUploader.open();
        });

        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'media-input-wrapper';
        inputWrapper.appendChild(urlInput);
        inputWrapper.appendChild(uploadBtn);

        wrapper.appendChild(label);
        wrapper.appendChild(inputWrapper);
        return wrapper;
    }
});
