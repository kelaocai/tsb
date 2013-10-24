//
//  PostListViewController.h
//  ipapp
//
//  Created by kelaocai on 13-9-29.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AFNetworking.h"
#import "ThreadListTableCell.h"
#import "MBProgressHUD.h"
@interface ThreadListViewController : UITableViewController <MBProgressHUDDelegate>
@property(strong,nonatomic)NSArray *threads;
@property (strong,nonatomic)NSString *fid;
@property (strong, nonatomic) MBProgressHUD *hud;
@property (retain, nonatomic) IBOutlet UITableView *postTableView;

@end
