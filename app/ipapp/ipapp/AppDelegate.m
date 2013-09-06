//
//  AppDelegate.m
//  ipapp
//
//  Created by kelaocai on 13-9-6.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "AppDelegate.h"
#import <SDWebImage/UIImageView+WebCache.h>

@implementation AppDelegate

- (void)dealloc
{
    [_window release];
    [super dealloc];
}

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    self.window = [[[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]] autorelease];
    // Override point for customization after application launch.
    self.window.backgroundColor = [UIColor whiteColor];
    
    UIView *uv=[[UIView alloc] initWithFrame:CGRectMake(140, 210, 40, 40)];
    uv.backgroundColor=[UIColor redColor];
    [self.window addSubview:uv];
    [uv release];
    
    UIImageView *uiv=[[UIImageView alloc] init];
    uiv.frame=CGRectMake(0, 0, 320, 100);
    [uiv setImageWithURL:[NSURL URLWithString:@"http://img.app.diandianzhe.com/banner/wxtx.png"]
        placeholderImage:[UIImage imageNamed:@"ss-blue"]];
    uiv.contentMode=UIViewContentModeScaleAspectFit;
    [self.window addSubview:uiv];
    [uiv release];
    
    [self.window makeKeyAndVisible];
    return YES;
}

- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

@end
