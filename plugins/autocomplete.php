<?php

/**
 * Autocomplete for keywords, tables and columns.
 * @author David Grudl
 * @license BSD
 */
class AdminerAutocomplete
{
    public $keywords = [
        'DELETE FROM', 'DISTINCT', 'EXPLAIN', 'FROM', 'GROUP BY', 'HAVING', 'INSERT INTO', 'INNER JOIN', 'IGNORE',
        'LIMIT', 'LEFT JOIN', 'NULL', 'ORDER BY', 'ON DUPLICATE KEY UPDATE', 'SELECT', 'UPDATE', 'WHERE',
    ];


    public function head()
    {
        if (!isset($_GET['sql'])) {
            return;
        }

        $suggests = [];
        foreach (array_keys(tables_list()) as $table) {
            $suggests[] = $table;
            foreach (fields($table) as $field => $foo) {
                $suggests[] = "$table.$field";
            }
        } ?>
        <style<?php echo nonce(); ?>>
            .ace_editor {
            width: 100%;
            height: 500px;
            resize: both;
            border: 1px solid black;
            }

            .shortcut-wrapper {
            padding: 8px;
            padding-left: 0;
            display: flex;
            gap: 8px;
            }
            </style>
            <script<?php echo nonce(); ?> src="static/ace/ace.js">
                </script>
                <script<?php echo nonce(); ?> src="static/ace/ext-language_tools.js">
                    </script>
                    <script<?php echo nonce(); ?>>
                        document.addEventListener('DOMContentLoaded', () => {
                        var keywords = <?php echo json_encode($this->keywords) ?>;
                        var suggests = <?php echo json_encode($suggests) ?>;
                        var preSQLArea = document.querySelector('pre.sqlarea');
                        preSQLArea.hidden = true;

                        var textarea = document.querySelector('textarea.sqlarea');
                        var form = textarea.closest("form");

                        const editorElem = document.createElement("div");
                        form.appendChild(editorElem)
                        var editor;

                        ace.config.set('basePath', 'static/ace');
                        editor = ace.edit(editorElem);
                        editor.setTheme('ace/theme/monokai');
                        editor.session.setMode('ace/mode/sql');
                        editor.setOptions({
                        fontSize: 14,
                        enableBasicAutocompletion: [{
                        identifierRegexps: [/[a-zA-Z_0-9\.\-\u00A2-\uFFFF]/], // added dot
                        getCompletions: (editor, session, pos, prefix, callback) => {
                        // note, won't fire if caret is at a word that does not have these letters
                        callback(null, [
                        ...keywords.map((word) => ({value: word + ' ', score: 1, meta: 'keyword'})),
                        ...suggests.map((word) => ({value: word + ' ', score: 2, meta: 'name'}))
                        ]);
                        },
                        }],
                        // to make popup appear automatically, without explicit ctrl+space
                        enableLiveAutocompletion: true,
                        });

                        textarea.hidden = true;
                        form.appendChild(textarea);
                        editor.getSession().on('change', () => {
                        textarea.value = editor.getSession().getValue();
                        });

                        // Shortcuts buttons
                        const buttonList = [
                        {
                        label: "SELECT",
                        query: "SELECT * \nFROM \"tmp\";\n"
                        },
                        {
                        label: "INSERT",
                        query: "INSERT INTO \"tmp\" (col1, col2) \nVALUES ('val1', 'val2');\n"
                        },
                        {
                        label: "UPDATE",
                        query: "UPDATE \"tmp\" \nSET col1 = 'val1', col2 = 'val2' \nWHERE 1=1;\n"
                        },
                        {
                        label: "DELETE",
                        query: "DELETE FROM \"tmp\" \nWHERE 1=1;\n"
                        },
                        ]

                        const shortcutWrapper = document.createElement("div");
                        shortcutWrapper.classList.add("shortcut-wrapper");
                        form.appendChild(shortcutWrapper)

                        for(const {label, query} of buttonList) {
                        const buttonElem = document.createElement("input")
                        buttonElem.type = "submit";
                        buttonElem.value = label;
                        buttonElem.addEventListener("click", (event) => {
                        event.preventDefault();
                        editor.insert(query)
                        });
                        shortcutWrapper.appendChild(buttonElem)
                        }

                        });
                        </script>
                <?php
            }
        }
