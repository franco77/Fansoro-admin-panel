# Morfy Media Plugin

Extends for Morfy Panel

##Documentation

Create a media.md file on storage/pages folder with template media.tpl like this:

		---
		title: Media example  
		description: Media items for gallery or portfolio 
		template: media  
		---


Create a media.tpl file on themes/default-theme folder like this:

File **media.tpl**

		{extends 'layout.tpl'}
		{block 'content'}
			<div class="container">
			    {Morfy::runAction('Media')}
			</div>	
		{/block}