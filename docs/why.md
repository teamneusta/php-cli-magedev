Magento is a complex system and it is a cumbersome task to set it up properly. Especially if you are working on multiple projects. This is where `magedev` aims to help. It automates multiple everyday tasks for magento projects. Working on multiple magento projects at the same time is not easy. They all have their own customizations and settings. Lets talk about the pain for a moment.

Assume you are asked to work on Magento project XY. If you are unlucky, you have not set it up on your local machine. Your only chance is to go through the pain to set it up by yourself. You need to install an apache server, configure your database with credentials, update your `local.xml` or `env.php`. If you are lucky, you are working on a project with some docker containers already preconfigured. But they are inconsistent. Not all of them have a mailcatcher for example. And guess what? The odds may be against you because this time you are asked to change some mail templates and no mailcatcher configured. So you start configuring it yourself. Everytime...

But you also need a database dump for working locally. Where to get it? If you got one, you need to align the base_url because this dump was gathered from a system with different configuration.

Then you want to log in to the backend. But credentials are not admin/admin123 anymore, because of a foreign database dump. Even the frontname for the backend may have changed for security reasons. So you struggle again, to align all of this to your needs.

