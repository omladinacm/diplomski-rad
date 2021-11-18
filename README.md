# Diplomski rad
Diplomski rad, Fakultet Računarskih nauka, UPIM

Ova aplikacija je završni, diplomski rad i predstavlja zbir stečenih znanja tokom četverogodišnjih studija na 
Fakultetu Računarskih nauka Univerziteta za Poslovni Inženjering i Menadžment

Autor rada: Dragan Marčeta

Mentor: prof. dr Velimir Dedić

## Neophodan software
Za potrebe ove aplikacije, lista neophodnog software-a je:
* [Docker Desktop](https://www.docker.com/products/docker-desktop)
* Windows Subsystem for Linux ([WSL](https://docs.microsoft.com/en-us/windows/wsl/install)) u slučaju da je OS Windows

## Pokretanje aplikacije
Pokretanje aplikacije se vrši izvršavanjem load komande iz run.sh bash skripte na slijedeći način:
  * Ako se koristi Windows, treba pokrenuti prvo WSL terminal, za MacOS i Linux samo pokrenuti postojeći Terminal
  * Promijeniti folder na folder ove aplikacije, npr. ```cd /home/user/diplomski-rad/```
  * Pokrenuti komandu ```./run.sh -m=env_create```
    * Rezultat ove komande je .env fajl koji bude kreiran u korijenskom folderu aplikacije (gdje se ujedno nalazi i 
      run.sh skripta)
    * Opciono, taj fajl je potrebno otvoriti sa nekim text editorom unutar terminala i izmijenti parametre za bazu 
      podataka
  * Zatim, pokrenuti sledeću komandu: ```./run.sh -m=load [-v]``` 
    * Argument -v je opcioni i pruža detaljni ispis run.sh skripte
    * Kada se komanda izvrši uspješno, prikaže se poruka done i aplikacija je dostupna preko **localhost** adrese unutar 
      web pretraživača

## Dodatne komande run.sh skripte
Dodatne komande run.sh skripte se mogu vidjeti pokretanjem ./run.sh -h ili ./run.sh --help komande

Neke od bitnijih su:
* ```load``` - Pokreće Docker kontejnere aplikacije
* ```unload``` - Isključuje Docker kontejnere aplikacije
* ```reload``` - Isključuje i ponovo uključuje Docker kontejnere aplikacije
* ```env_create``` - Kreira ```.env``` fajl koji je neophodan za pokretanje baze podataka