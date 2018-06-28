import { Component, OnInit } from "@angular/core";
import User from "../../models/user";
import { FormGroup, FormControl } from "@angular/forms";
import { Http } from "@angular/http";
import { Router } from "@angular/router";
import { LoginService } from "../../services/login.service";

@Component({
  selector: "app-login",
  templateUrl: "./login.component.html",
  styleUrls: ["./login.component.scss"]
})
export class LoginComponent {
  loginForm = new FormGroup({
    email: new FormControl(),
    password: new FormControl()
  });

  token: User;
  constructor(
    private http: Http,
    private router: Router,
    private loginService: LoginService
  ) {
    if (localStorage.getItem("token") != null) {
      this.router.navigate(["/"]);
    } else {
      this.router.navigate(["/login"]);
    }
  }

  login(login: User): void {
    this.token = JSON.parse(localStorage.getItem("token"));
    this.loginService.callService(login).subscribe(data => {
      localStorage.setItem("token", data["token"]);

      this.router.navigate([""]);
    });
  }
}
