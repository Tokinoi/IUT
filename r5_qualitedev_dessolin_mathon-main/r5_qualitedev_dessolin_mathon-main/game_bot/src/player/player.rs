use std::ffi::CString;
use std::io::Write;
use std::net::TcpStream;
use std::thread;
use std::time::Duration;

pub(crate) struct Player {
    position: f64,
    orientation: f64,
    name : String,
    stream : TcpStream
}

impl Player {
    fn new(position: f64, orientation: f64, name : String, ip : string) -> Player {
        stream.write(b"NAME=Gloubidou 2, le retour\r").expect("PASS");
        let mut stream = TcpStream::connect(ip)?;
        Player { position, orientation, name, stream }
    }
    fn action(){
       self.color();
        // Lire les données du serveur
        let mut buffer = [0; 512];
        let mut buffer_proj = [0; 512];
        stream.write(b"CBOT\n").expect("PASS");
        stream.read(&mut buffer).expect("Erreur lors de la lecture des données du serveur");
        let message = String::from_utf8_lossy(&buffer[..]);
        stream.write(b"CPROJ\n").expect("PASS");
        stream.read(&mut buffer_proj).expect("Erreur lors de la lecture des données du serveur");
        let _project = String::from_utf8_lossy(&buffer_proj[..]);

        // Analyser les données
        // Récupérer l'orientation et la distance de l'adversaire le plus proche


        let info_play: Vec<&str> = message.split("\r").collect();
        let bot_info: Vec<&str> = info_play[0].split("/").collect();

        if bot_info == ["EMPTY"] {
            return
        }
        let orientation: f32 = bot_info[0].parse().expect("0");
        let _distance: f32 = bot_info[1].parse().expect("0");


        let info_proj: Vec<&str> = _project.split("\r").collect();
        let proj_info: Vec<&str> = info_proj[0].split("/").collect();
        println!("{:?}", proj_info);

        if proj_info == ["EMPTY"] {
            return
        }
        let orientation_proj: f32 = proj_info[0].parse().expect("0");
        let distance_proj: f32 = proj_info[1].parse().expect("0");


        // Choisir une action en fonction des informations sur l'adversaire
        // L'adversaire est proche, tirer
        // L'adversaire est loin, se déplacer vers lui

        println!("{:?}", orientation_proj);
        if distance_proj < 15.0 {
            stream.write(b"GunTrig=1.0\r").expect("Erreur lors de l'envoi de la commande de tir");
            stream.write(format!("GunTrav={orientation_proj}\r", orientation_proj = orientation_proj).as_ref()).expect("0");
        } else {
            if _distance < 20.0 {
                stream.write(b"GunTrig=1.0\n").expect("Erreur lors de l'envoi de la commande de tir");
                stream.write(format!("GunTrav={orientation}\r", orientation = orientation).as_ref()).expect("0");
            } else { stream.write(b"GunTrig=0.0\r").expect("Erreur lors de l'envoi de la commande de tir"); }
        }
        if orientation < 0.5 {
            stream.write(b"MotR=0.5\r").expect("0");
            stream.write(b"MotL=1.0\r").expect("0");
        } else {
            stream.write(b"MotR=1.0\r").expect("0");
            stream.write(b"MotL=0.5\r").expect("0");
        }

    }

    fn color(){
        let mut rng = rand::thread_rng();
        let red = rng.gen_range(0..255);
        let green = rng.gen_range(0..255);
        let blue = rng.gen_range(0..255);

        stream.write(format!("COL={red}={green}={blue}#\r", red = red, green = green, blue = blue).as_ref()).expect("PASS");
    }
}