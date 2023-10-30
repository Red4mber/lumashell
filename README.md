# lumashell

It's a PHP reverse shell and web shell, with support for custom functions and multiple backupexec functions if some get disabled.

Default exec function uses proc_open so it should be reliable enough.
I didn't have the time to test it on a windows host, but we'll see later i promise :3

So far it supports only proc_open, exec, passthru, system and shell exec as a way to execute code, but i plan on adding more in the future

## Authentication

I know it is unfinished and ugly and i should have do a login form, 
but php sucks, the less i do it, the happier i am.

To authenticate on it you need a cookie set to the values you see in line 3 and 4
> And **yes**, The cookie shall contain the cleartext of the password,
> else you just invented pass-the-hash on the web, well done, but that's not the goal

you can specify the name of the cookie you want, in case of conflict with whatever other cookie you already have

if you have the right cookie, but not the right value, it'll print the hash of whatever is the value of your cookie
you'll have to either edit the script yourself or type the right password and try again

also I know the auth with BCRYPT is overkill but better safe than sorry


### TODO - Heres a list of everything it doesn't yet do
So many thing to do, so little time...
- Test if everything works on a windows server
- Finish file upload functionnality (which rn is just a form that does nothing)
- touch up the visual aspect of it (and keep the history of commands on screen)
- do an actual login form so we don't have to create the cookie by hand like cavemen 

Maybe i'll do some obfuscation but i want to keep it clean and readable, it's a learning project kinda and i don't need stealth often
