<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2013 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|   
+---------------------------------------------------------------------------
*/

/**
 * 仅附件处理中的preg_replace_callback()的每次搜索时的回调
 * @param  array $matches preg_replace_callback()搜索时返回给第二参数的结果
 * @return string  取出附件的加载模板字符串
 */
function tsb_parse_attachs_callback($matches)
{
	if ($attach = AWS_APP::model('publish')->get_attach_by_id($matches[1]))
	{
		TPL::assign('attach', $attach);
		
		return TPL::output('tsbm/ajax/load_attach', false);
	}
}