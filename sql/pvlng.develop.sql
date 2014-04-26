--
-- For development branch only!
--

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('app', 'en', 'DragDropHelp', '- Drag a group or channel here for append to top level\r\n- Use Ctrl+Click to start copy of channel\r\n- You can\'t copy groups, create an alias and use this instead', 0),
('app', 'de', 'DragDropHelp', '- Ziehe eine Gruppe oder Kanal hierher für oberste Ebene\r\n- Benutze Strg-Klick um Kanäle zu kopieren\r\n- Gruppen können nicht kopiert werden, erstelle einen Alias und nutze diesen', 0),
('app', 'de', 'Information', 'Information', 0),
('app', 'en', 'CantCopyGroups', 'You can\'t copy groups!\r\nCreate an alias and use this instead.', 0),
('app', 'de', 'CantCopyGroups', 'Du kannst keine Gruppen kopieren!\r\nErstelle bitte einen Alias für diese und nutze ihn.', 0),
('app', 'de', 'ClearSearch', 'Suchbegriff löschen', 0),
('app', 'en', 'ClearSearch', 'Clear search term', 0);
