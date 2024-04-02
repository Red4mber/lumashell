# lumashell

This is my PHP reverse shell. There are many like it, but this one is mine.

I made this in an afternoon, so don't expect for it to be the best webshell, but it does the job and has a few nice functionalities, with support for custom functions and multiple backup exec functions if some get disabled.


Default exec function uses proc_open so it should be reliable enough.
I didn't have the time to test it on a windows host, but we'll see later i promise :3

So far it supports only proc_open, exec, passthru, system and shell exec as a way to execute code, maybe I'll add more in the future

### Authentication

To authenticate on it you need a cookie set to the values you see in line 3 and 4
> And **yes**, The cookie shall contain the cleartext of the password,
> else you just invented pass-the-hash on the web, well done, but that's not the goal

if you have the right cookie, but not the right value, it'll print the hash of whatever is the value of your cookie
you'll have to either edit the script yourself or type the right password and try again


### TODO - Heres a list of everything it doesn't yet do
So many thing to do, so little time...
- Test if everything works on a windows server
- Finish file upload functionnality (which rn is just a form that does nothing)
- touch up the visual aspect of it (and keep the history of commands on screen)
- do an actual login form so we don't have to create the cookie by hand like cavemen 

Maybe i'll do some obfuscation but i want to keep it clean and readable, it's a learning project kinda and i don't need stealth often
