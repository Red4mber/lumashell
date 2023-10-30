# lumashell


I know the auth with BCRYPT is overkill but better safe than sorry

Php reverse shell and web shell, with support for custom functions and multiple backupexec functions if some get disabled.
Default exec function uses proc_open so it should be reliable enough.
I didn't have the time to test it on a windows host, but we'll see later i promise :3

I know it is unfinished and ugly, but php sucks ass, the less i do it, the happier i am.

So far it supports only proc_open, exec, passthru, system and shell exec as a way to execute code, but i plan on adding more in the future

Maybe i'll do some obfuscation but i want to keep it clean and readable, it's a learning project kinda
 






### TODO - Heres a list of everything it doesn't yet do
So many thing to do, so little time...
- Test if everything works on a windows server
- Finish file upload functionnality (which rn is just a form that does nothing)
- touch up the visual aspect of it (and keep the history of commands on screen)
- do an actual login form so we don't have to create the cookie by hand like cavemen 
