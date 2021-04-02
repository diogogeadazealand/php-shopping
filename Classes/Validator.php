<?php 

class Validator{
    const ID = "/^[0-9]*$/";
    const NUMBER = "/^[0-9\.]*$/";
    const TEXT = "/^[0-9A-Z-a-z\s]*$/";
    const EMAIL = "/^[A-Za-z-_.0-9]*@[A-Za-z0-9.]*.[A-Za-z]{2,3}$/";
    const PHONE = "/^[0-9]{8}$/";
    const ADDRESS = "/^[A-Za-z0-9,\s]*$/";
    const PASSWORD = "/^[A-Za-z0-9-\.\_\,\s\!\?\#]*$/";
}