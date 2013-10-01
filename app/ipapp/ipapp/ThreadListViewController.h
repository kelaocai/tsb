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
@interface PostListViewController : UITableViewController
@property(strong,nonatomic)NSArray *posts;
@property (strong,nonatomic)NSString *fid;
@property (retain, nonatomic) IBOutlet UITableView *postTableView;

@end
