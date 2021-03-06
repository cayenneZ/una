-- CONTENT INFO
DELETE FROM `sys_objects_content_info` WHERE `name` IN ('bx_snipcart', 'bx_snipcart_cmts');
INSERT INTO `sys_objects_content_info` (`name`, `title`, `alert_unit`, `alert_action_add`, `alert_action_update`, `alert_action_delete`, `class_name`, `class_file`) VALUES
('bx_snipcart', '_bx_snipcart', 'bx_snipcart', 'added', 'edited', 'deleted', '', ''),
('bx_snipcart_cmts', '_bx_snipcart_cmts', 'bx_snipcart', 'commentPost', 'commentUpdated', 'commentRemoved', 'BxDolContentInfoCmts', '');

DELETE FROM `sys_content_info_grids` WHERE `object`='bx_snipcart';
INSERT INTO `sys_content_info_grids` (`object`, `grid_object`, `grid_field_id`, `condition`, `selection`) VALUES
('bx_snipcart', 'bx_snipcart_administration', 'id', '', ''),
('bx_snipcart', 'bx_snipcart_common', 'id', '', '');


-- SEARCH EXTENDED
DELETE FROM `sys_objects_search_extended` WHERE `module`='bx_snipcart';
INSERT INTO `sys_objects_search_extended` (`object`, `object_content_info`, `module`, `title`, `active`, `class_name`, `class_file`) VALUES
('bx_snipcart', 'bx_snipcart', 'bx_snipcart', '_bx_snipcart_search_extended', 1, '', ''),
('bx_snipcart_cmts', 'bx_snipcart_cmts', 'bx_snipcart', '_bx_snipcart_search_extended_cmts', 1, 'BxTemplSearchExtendedCmts', '');
