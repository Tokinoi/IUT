use std::io::prelude::*;
use std::net::TcpStream;
use std::{env, thread};
use std::time::Duration;
use rand::Rng;

fn main() -> std::io::Result<()> {
    let args: Vec<String> = env::args().collect();
    let mut ip: String = args[1].to_owned();
    ip.push_str(":");
    ip.push_str(&args[2]);



    loop {
        thread::sleep(Duration::from_millis(1));
    }
}
